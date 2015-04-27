<?php
/**
 * @file
 * Automatically generates Drush aliases from directory structure.
 */

define('GENERATE_DRUSH_ALIASES_DEFAULT', 'default.aliases.drushrc.php');

class DrushAlias {

  protected $defaults;
  protected $path;
  protected $suffix = 'dev';

  /**
   * Parameter checks, initialize defaults.
   */
  public function __construct() {

    if (!isset($GLOBALS['argv'][1])) {
      print "Usage: php generate_aliases.php /path/to/docroots [hostname-suffix]" . PHP_EOL;
      exit(1);
    }

    if (!empty($GLOBALS['argv'][2])) {
      $this->suffix = $GLOBALS['argv'][2];
    }

    $this->path = $GLOBALS['argv'][1];
    if (!is_dir($this->path)) {
      throw new Exception($this->path . ' is not a valid path');
    }

    if (file_exists(GENERATE_DRUSH_ALIASES_DEFAULT)) {
      include GENERATE_DRUSH_ALIASES_DEFAULT;
      if (isset($defaults)) {
        $this->defaults = $defaults;
      }
    }
  }

  /**
   * Outputs a header for the drush aliases file.
   */
  protected function header() {
    print '<?php';
    print PHP_EOL;
    print '/**' . PHP_EOL;
    print ' * Your own custom alias file courtesy of https://github.com/mrf/generate-drush-aliases' . PHP_EOL;
    print ' */';
    print PHP_EOL;
    print PHP_EOL;
  }

  /**
   * Outputs a single alias entry.
   */
  protected function buildAlias($value, $entry) {
    $alias = array(
      'uri' => $value . '.' . $this->suffix,
      'root' => realpath($entry),
    );
    if (isset($this->defaults)) {
      $alias += $this->defaults;
    }
    print "// $value";
    print PHP_EOL;
    print "\$aliases['" . $value . "'] = ";
    print var_export($alias) . ';';
    print PHP_EOL . PHP_EOL;
  }

  /**
   * Scans for Drupal instances and outputs the Drush aliases file.
   */
  public function generate() {
    $this->header();
    if ($scanned_directory = array_diff(scandir($this->path), array('..', '.'))) {
      foreach ($scanned_directory as $value) {
        $entry = $this->path . '/' . $value;
        if (is_dir($entry)) {
          chdir($entry);
          if (!file_exists('index.php') && file_exists('docroot' . DIRECTORY_SEPARATOR . 'index.php')) {
            $entry .= DIRECTORY_SEPARATOR . 'docroot';
            chdir($entry);
          }
          $version = shell_exec('drush core-status --format=list version');
          if ($version && $version !== '7.0-dev') {
            if (file_exists('index.php')) {
              $this->buildAlias($value, $entry);
            }
          }
        }
      }
    }
  }

}

(new DrushAlias())->generate();
