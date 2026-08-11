<?php

  function examplesBuild () {

    foreach ( padAppsList () as $one ) {

      set_time_limit ( 60 );

      examplesBuildPage ( $one ['app'], $one ['item'] );

    }

  }

  function examplesBuildPage ( $app, $item ) {

    global $padHost;

    $include = ( $item != 'index' ) ? '&padInclude' : '';

    $curl = padCurl ( "$padHost$app/?$item$include" );

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

?>