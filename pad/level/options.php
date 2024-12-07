<?php

  $padOptionsSingle = $padOptionsMulti = [];

  foreach ( $padPrmParse as $padV ) {

    list ( $padPrmName, $padPrmValue ) = padSplit ( '=', trim ( $padV ) );

    if ( padValidVar ( $padPrmName ) ) {

      if ( $padInfo )
        include 'events/option.php';

      $padPrmValue = ( $padPrmValue === '' ) ? TRUE : padEval ( $padPrmValue );

      $padOptionsSingle [$padPrmName] = $padPrmValue;
      $padOptionsMulti  []            = [ 'padPrmName' => $padPrmName, 'padPrmValue' => $padPrmValue ];
  
    }

  }

?>