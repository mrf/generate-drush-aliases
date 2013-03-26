<?php
if($project = opendir('/path/to/sites')) {
  while (false !== ($entry = readdir($project))) {
    if('..' !== $entry && '.' !== $entry && is_dir($entry)) {
      print "//$entry\n";
      print "\$aliases['" . $entry . "'] = array(\n";
      print "    'uri' => '" . $entry . ".mysite.com',\n";
      print "    'root' => '/path/to/sites/" . $entry . "',\n";
      print "  );\n";
      print "\n";
    }
  }
}
