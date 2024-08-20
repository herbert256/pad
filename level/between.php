<?php

  $padFirst    = substr ( $padBetween , 0, 1 );
  $padWords    = preg_split ( "/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY );
  $padTagCheck = $padWords [0] ?? '';
  $padTagOpts  = $padWords [1] ?? '';
  $padPrmParse = padParseOptions ( $padTagOpts );

  $padStartOptionsSingle = $padStartOptionsMulti = [];

  foreach ( $padPrmParse as $padV ) {

    list ( $padPrmName, $padPrmValue ) = padSplit ( '=', trim ( $padV ) );

    if ( padValidVar ( $padPrmName ) ) {

      $padPrmValue = ( $padPrmValue === '' ) ? TRUE : padEval ( $padPrmValue );

      $padStartOptionsSingle [$padPrmName] = $padPrmValue;
      $padStartOptionsMulti  []            = [ 'padPrmName' => $padPrmName, 'padPrmValue' => $padPrmValue ];
  
    }

  }

?>