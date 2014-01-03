<?php
$path = $argv[1];
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

function build_alias($value,$entry) {
  print "// $value";
  print PHP_EOL;
  print "\$aliases['" . $value. "'] = array(" . PHP_EOL;
  print "  'uri' => '" . $value . ".dev'," . PHP_EOL;
  print "  'root' =>  $entry," . PHP_EOL;
  print ");" . PHP_EOL;
  print PHP_EOL;
}

if($scanned_directory = array_diff(scandir($path), array('..', '.'))) {
 foreach($scanned_directory as $value) {
   $entry = $path . '/' . $value;
   if(is_dir($entry)) {
     chdir($entry);
     $version = shell_exec('drush core-status --format=list version');
     if($version && $version !== '7.0-dev') {
       if(file_exists('index.php')) {
         build_alias($value,$entry);
       } else if (is_dir('docroot')) {
         if(file_exists('index.php')) {
           // Special logic for Acquia repos
           chdir('docroot');
           $entry = $entry . '/docroot';
           build_alias($value,$entry);
         }
       }
     }
   }
 }
}
