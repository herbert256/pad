<?php

  // The per-request initialisation running order, and the only file that decides it.
  //
  // Called from start/pad/go.php once error handling exists, and again by start/restart.php
  // when a page restarts, hence the include_once on the two steps that must not be repeated
  // (const.php defines constants, lib.php declares functions).
  //
  // The order matters: variables and buffers first, then the page to run must be resolved
  // before config is read (the application's _config/config.php may depend on it), config
  // before anything that reads a setting, error handling before the first thing that can
  // fail, and the level arrays before parms and the hand-over to the application in app.php.

  if ( ! isset ( $padMicro ) ) $padMicro = microtime ( TRUE );
  if ( ! isset ( $padHR    ) ) $padHR    = hrtime    ( TRUE );

  include_once PAD . 'inits/const.php';
  include_once PAD . 'inits/lib.php';

  include PAD . 'inits/vars.php';
  include PAD . 'inits/clean.php';
  include PAD . 'inits/page.php';
  include PAD . 'inits/ids.php';
  include PAD . 'inits/config.php';
  include PAD . 'inits/nono.php';
  include PAD . 'inits/fast.php';
  include PAD . 'inits/error.php';
  include PAD . 'inits/cookies.php';
  include PAD . 'inits/client.php';
  include PAD . 'inits/host.php';
  include PAD . 'inits/info.php';
  include PAD . 'inits/cache.php';
  include PAD . 'inits/level.php';
  include PAD . 'inits/parms.php';
  include PAD . 'inits/app.php';

?>