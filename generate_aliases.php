#!/usr/bin/env php
<?php
global $project, $uri_base, $key;

$options = getopt("f:p::u::k::", array("folder:","project::i", "uri_base::", "key::"));

$path = !empty($options['f']) ? $options['f'] : null;
$project = !empty($options['p']) ? $options['p'] : null;
$uri_base = !empty($options['u']) ? $options['u'] : null;
$key = !empty($options['k']) ? $options['k'] : null;

if(!is_dir($path)) {
  print $path . ' is not a valid path';
  exit;
}

print '<?php';
print PHP_EOL;
print '/**' . PHP_EOL;
print ' * Your own custom alias file courtesy of https://github.com/mrf/generate-drush-aliases' . PHP_EOL;
print ' */';
print PHP_EOL;
print PHP_EOL;

function build_alias($value, $entry) {
  global $project, $uri_base, $key;
  if(empty($project)) {
    $project = $value;
  }
  if(empty($uri_base)){
    $uri_base = 'dev';
  }
  print "// $project";
  print PHP_EOL;
  print "\$aliases['" . ( !empty($key) ? $key : $project ). "'] = array(" . PHP_EOL;
  print "  'uri' => '$project.$uri_base'," . PHP_EOL;
  print "  'root' =>  '$entry'," . PHP_EOL;
  print ");" . PHP_EOL;
  print PHP_EOL;
}

if($scanned_directory = array_diff(scandir($path), array('..', '.'))) {
 $path = realpath($path);
 foreach($scanned_directory as $value) {
   $entry = $path . '/' . $value;
   if(!is_link($entry) && is_dir($entry)) {
     chdir($entry);
     $version = shell_exec('drush core-status --format=list "Drupal version"');
     if($version && $version !== '7.0-dev') {
       if(file_exists('index.php')) {
         build_alias($value, $entry);
         continue;
       } else if (is_dir('docroot')) {
         if(file_exists('index.php')) {
           // Special logic for Acquia repos
           chdir('docroot');
           $entry = $entry . '/docroot';
           build_alias($value, $entry);
           continue;
         }
       }
     }
   }
 }
}
