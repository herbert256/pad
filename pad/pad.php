<?php

  // Framework entry point: turns a caller-supplied application selection into a running request.
  //
  // The caller (www/pad.php for the web, apps/cli/pad for the command line) must already have
  // set $padApps, $padApp and $padData. This file normalises those, records the request start
  // time ($padMicro / $padHR, later reported by the stats info type), defines the path
  // constants the rest of the engine is built on - PAD, APP, DATA, APPS, COMMON - makes the
  // application directory both the working directory and the include path, and then hands over
  // to start/pad.php, which boots error handling, config and the level loop.

  if ( ! isset ( $padMicro ) ) $padMicro = microtime ( TRUE );
  if ( ! isset ( $padHR )    ) $padHR    = hrtime    ( TRUE );

  if ( ! isset ( $padApps ) ) die ( 'Variable $padApps must be set before calling this script' );
  if ( ! isset ( $padApp  ) ) die ( 'Variable $padApp must be set before calling this script' );
  if ( ! isset ( $padData ) ) die ( 'Variable $padData must be set before calling this script' );

  if ( ! str_ends_with ( $padApps, '/' ) ) $padApps .= '/';
  if ( ! str_ends_with ( $padData, '/' ) ) $padData .= '/';
 
  define ( 'PAD',    dirname ( __FILE__ ) . '/' );
  define ( 'APP',    $padApps . $padApp . '/'   );
  define ( 'DATA',   $padData                   );
  define ( 'APPS',   $padApps                   );
  define ( 'COMMON', $padApps . '_common/'      );

  if ( ! file_exists (APP) or ! is_dir (APP) ) die ( "Application directory not found: " . APP );

  chdir            ( APP );
  set_include_path ( APP );

  include PAD . 'start/pad.php';

?>