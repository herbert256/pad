<?php

  if ( file_exists ( APP . "_options/$padPrmName.php") )
    $padOptionsAppStart [$pad] [] = $padPrmName;

  $padPrm [$pad] [$padPrmName] = $padOptionsSingle [$padPrmName];

  $padPrmEval = ( $padPrm [$pad] [$padPrmName] === TRUE ) ? $padPrmName : $padPrm [$pad] [$padPrmName];

  $padParmsSetType  = 'option';
  $padParmsSetName  = $padPrmName;
  $padParmsSetValue = $padPrmEval;


?>