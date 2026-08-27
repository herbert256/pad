<?php

  function referenceBuild () {

    foreach ( padAppsList () as $one ) {

      set_time_limit ( 60 );

      referenceBuildPage ( $one ['app'], $one ['item'] );

    }

  }

  function referenceBuildPage ( $app, $item ) {

    global $padHost;

    $include = ( $item != 'index' ) ? '&padInclude' : '';

    $curl = padCurl ( "$padHost$app/?$item$include&padReference" );

  }

?>