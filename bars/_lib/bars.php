<?php

  function bars ( $in ) {

    foreach ( glob ( "$in\intace_is-*\overlays\*\patch-integrationserver.yaml" ) as $file ) {

      $parts = explode ( 'intace_is-', $file      );
      $parts = explode ( "\\",         $parts [1] );

      $is  = $parts [0];
      $env = $parts [2];

      $bars = yaml_parse_file ( $file );
      $bars = explode ( ',' , $bars ['spec'] ['barURL'] );

      foreach ( $bars as $bar ) {

        $bar = str_replace ( '.bar', '', $bar );

        $split = explode ( '/' , $bar);
        $split = explode ( '-' , end ($split) );

        if ( $split [0] == 'abz' ) { // There is always an exception to the naming conventions ...
          array_shift ( $split);
          $split [0] = 'abz-' . $split [0];
        }

        $bar     = strtolower ( array_shift ( $split ) );
        $version = implode ( '-' , $split );

        if ( $version == '0.0.0' )  // Old WMB-7 bars have 0.0.0 as version.
          $version = 'n/a';

        $ace [$is] [$bar] [$env] = $version;   

      }

    }

    return $ace ?? [];

  }

?>