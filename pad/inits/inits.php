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
  // The CSRF check stands just before parms, so a post it turns away never becomes
  // variables.
  //
  // Once the host is known, and before anything can answer: the CORS headers (a preflight
  // ends there), maintenance (503 for every page, before the page cache), then the
  // engine's own answers for a page the application does not have - sitemap.xml and
  // robots.txt, the health check ?up - and the 404 of any other, which waited for the
  // configuration to know whether the application renders its own error page.

  if ( ! isset ( $padMicro ) ) $padMicro = microtime ( TRUE );
  if ( ! isset ( $padHR    ) ) $padHR    = hrtime    ( TRUE );

  include_once PAD . 'inits/const.php';
  include_once PAD . 'inits/lib.php';

  include PAD . 'inits/route.php';
  include PAD . 'inits/vars.php';
  include PAD . 'inits/clean.php';
  include PAD . 'inits/page.php';
  include PAD . 'inits/ids.php';
  include PAD . 'inits/config.php';
  include PAD . 'inits/nono.php';
  include PAD . 'inits/error.php';
  include PAD . 'inits/cookies.php';
  include PAD . 'inits/client.php';
  include PAD . 'inits/host.php';
  include PAD . 'inits/cors.php';
  include PAD . 'inits/maintenance.php';
  include PAD . 'inits/sitemap.php';
  include PAD . 'inits/health.php';
  include PAD . 'inits/notFound.php';
  include PAD . 'inits/reload.php';
  include PAD . 'inits/locale.php';
  include PAD . 'inits/sample.php';
  include PAD . 'inits/info.php';
  include PAD . 'inits/cache.php';
  include PAD . 'inits/level.php';
  include PAD . 'inits/csrf.php';
  include PAD . 'inits/flash.php';
  include PAD . 'inits/parms.php';
  include PAD . 'inits/app.php';

?>
