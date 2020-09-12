<?php

  $pad_boot = microtime (true);

  //  ============================================================================
  //  PAD 10.0  - (P)HP (A)pplication (D)river
  //  (c) 2004-2020 - Herbert Groot Jebbink - herbert@groot.jebbink.nl
  //  ============================================================================
  //
  //  This is the PAD startup file, the first file that becomes active.
  //
  //  Only this file must be located inside the webservers htdocs directory,
  //  all other PAD files must be stored *OUTSIDE* the webservers htdocs directory.
  //
  //  ============================================================================

  define ( 'PAD_HOME', '/var/www/pad/'  );  // Home of the PAD framework files
  define ( 'PAD_APPS', '/var/www/apps/' );  // Home of the PAD applications
  define ( 'PAD_DATA', '/tmp/pad/' );       // Data locaction, used for logs/cache/errors/etc.

  include PAD_HOME . 'pad.php';

?>