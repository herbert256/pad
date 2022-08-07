<?php

  $pTimings_start ['init'] = microtime(true);
 
  ob_start();

  set_include_path('');

  $pLib = PAD . 'lib';
  include 'lib.php';

  include 'app.php';
  include 'page.php';
  include 'ids.php';
  include 'vars.php';
  include 'config.php';
  include 'nono.php';
  include 'fast.php';
  include 'error.php';
  include 'trace.php';
  include 'cookies.php';
  include 'zip.php';
  include 'host.php';
  include 'cache.php';
  include 'options.php';
  include 'parms.php';

  $pLib = APP . 'lib';
  include 'lib.php';

  include PAD . 'level/setup.php';
  include PAD . 'build/build.php';

  pTiming_end ('init');

?>