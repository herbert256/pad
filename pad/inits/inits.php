<?php

  $padTimings_start ['init'] = microtime(true);
 
  ob_start();

  include 'libPad.php';
  include 'app.php';
  include 'page.php';
  include 'ids.php';
  include 'vars.php';
  include 'config.php';
  include 'trace.php';
  include 'nono.php';
  include 'fast.php';
  include 'error.php';
  include 'cookies.php';
  include 'zip.php';
  include 'host.php';
  include 'cache.php';
  include 'level.php';
  include 'options.php';
  include 'parms.php';
  include 'libApp.php';
  include 'build.php';

  padTimingEnd ('init');

?>