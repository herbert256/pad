<?php

  $padOptionCheck = padOptionCheck ( $padPrmName );

  if ( $padOptionCheck ) {
    $padOptionsAppStart     [$pad] []            = $padPrmName;
    $padOptionsAppStartCall [$pad] [$padPrmName] = $padOptionCheck;
  }

  $padPrm [$pad] [$padPrmName] = ( $padPrmValue === '' ) ? TRUE : padEval ( $padPrmValue );

  $padParmsSetType  = 'option';
  $padParmsSetName  = $padPrmName;
  $padParmsSetValue = $padPrm [$pad] [$padPrmName];

?>