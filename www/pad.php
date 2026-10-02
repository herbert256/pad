<?php

  // The bootstrap every entry point includes, never a page of its own. Requested directly,
  // there is no application to derive: with www/ as the docroot the application came out
  // empty and the whole apps/ tree was served as one (under a mount prefix the directory
  // name happened to pick an application). That request is answered 404.

  if ( realpath ( $_SERVER ['SCRIPT_FILENAME'] ?? '' ) === __FILE__ ) {
    http_response_code ( 404 );
    return;
  }

  include __DIR__ . '/../home/home.php';

  $padApps = "$padHome/apps/";
  $padData = "$padHome/DATA/";

  // The application is the entry point's directory relative to www/ - realpath()ed on both
  // sides, so a docroot symlink does not hide the match - and it may be nested: the entry
  // www/regression/main/ bootstraps the application 'regression/main'. The mount prefix is
  // what is left of SCRIPT_NAME once the application part is taken off the end.

  $padDir  = dirname ( $_SERVER ['SCRIPT_NAME'] );
  $padWww  = str_replace ( '\\', '/', __DIR__ );
  $padReal = str_replace ( '\\', '/', realpath ( dirname ( $_SERVER ['SCRIPT_FILENAME'] ) ) );

  if ( $padReal != $padWww and str_starts_with ( $padReal, "$padWww/" ) )
    $padApp = substr ( $padReal, strlen ( $padWww ) + 1 );
  else
    $padApp = basename ( $padDir );

  $padRoot = substr ( $padDir, 0, strlen ( $padDir ) - strlen ( $padApp ) );

  unset ( $padWww, $padReal );

  include "$padHome/pad/pad.php";

?>
