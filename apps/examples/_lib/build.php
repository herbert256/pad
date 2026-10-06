<?php

  function examplesBuild () {

    foreach ( padAppsList () as $one ) {

      set_time_limit ( 60 );

      examplesBuildPage ( $one ['app'], $one ['item'] );

    }

  }

  function examplesBuildPage ( $app, $item ) {

    global $padHost;

    if ( examplesGuarded ( $app, $item ) )
      return;

    $curl = padCurl ( "$padHost$app/?$item&padInclude" );

    if ( ! str_starts_with ( $curl ['result'], '2' ) )
      return;

    $source = padFileGet ( APPS . "$app/$item.pad"  )
            . padFileGet ( APPS . "$app/$item.html" )
            . padFileGet ( APPS . "$app/$item.php"  );

    if ( str_contains ( $source, '{page'    ) ) return;
    if ( str_contains ( $source, '{example' ) ) return;
    if ( str_contains ( $source, '{ajax'    ) ) return;
    if ( str_contains ( $source, '{table'   ) ) return;
    if ( str_contains ( $source, '{demo'    ) ) return;

    if ( file_exists ( APPS . "$app/$item.php" ) )
      padFilePut ( "examples/$app/$item.php",  padFileGet ( APPS . "$app/$item.php" ) );

    if ( file_exists ( APPS . "$app/$item.pad" ) )
      padFilePut ( "examples/$app/$item.pad",  padFileGet ( APPS . "$app/$item.pad" ) );
    elseif ( file_exists ( APPS . "$app/$item.html" ) )
      padFilePut ( "examples/$app/$item.pad",  padFileGet ( APPS . "$app/$item.html" ) );

    padFilePut ( "examples/$app/$item.html", padTidySmall ( $curl ['data'], TRUE ) );

  }

  // A page behind a _guard.php - in its own directory or any above it, up to the application
  // root - is no example: what a crawl gets there is the guard's answer, a refusal or a
  // login page, and a login page carries its session's own CSRF token, so every harvest
  // stored a different file. The sitemap leaves the same pages out (lib/sitemap.php).

  function examplesGuarded ( $app, $item ) {

    $dir   = APPS . "$app/";
    $parts = explode ( '/', $item );

    array_pop ( $parts );

    if ( file_exists ( $dir . '_guard.php' ) )
      return TRUE;

    foreach ( $parts as $part ) {
      $dir .= "$part/";
      if ( file_exists ( $dir . '_guard.php' ) )
        return TRUE;
    }

    return FALSE;

  }

?>
