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

  // A start that cannot happen fails: die () with a message ended the process with status
  // 0, so a command line, a CI step or a script running this file saw success, and a web
  // request answered the message with a 200. Now the message goes to the error stream with
  // status 1 on the command line, and as a plain-text 500 on the web.

  $padBootFail = function ( $message ) {

    if ( PHP_SAPI == 'cli' ) {
      fwrite ( STDERR, "$message\n" );
      exit ( 1 );
    }

    if ( ! headers_sent () ) {
      http_response_code ( 500 );
      header ( 'Content-Type: text/plain; charset=UTF-8' );
    }

    echo $message;
    exit ( 1 );

  };

  if ( ! isset ( $padApps ) ) $padBootFail ( 'Variable $padApps must be set before calling this script' );
  if ( ! isset ( $padApp  ) ) $padBootFail ( 'Variable $padApp must be set before calling this script' );
  if ( ! isset ( $padData ) ) $padBootFail ( 'Variable $padData must be set before calling this script' );

  if ( ! str_ends_with ( $padApps, '/' ) ) $padApps .= '/';
  if ( ! str_ends_with ( $padData, '/' ) ) $padData .= '/';
 
  define ( 'PAD',    dirname ( __FILE__ ) . '/' );
  define ( 'APP',    $padApps . $padApp . '/'   );
  define ( 'DATA',   $padData                   );
  define ( 'APPS',   $padApps                   );
  define ( 'COMMON', $padApps . '_common/'      );

  if ( ! file_exists (APP) or ! is_dir (APP) ) $padBootFail ( "Application directory not found: " . APP );

  unset ( $padBootFail );

  chdir            ( APP );
  set_include_path ( APP );

  include PAD . 'start/pad.php';

?>
