<?php

  if ( file_exists ( APP . "_options/$padPrmName.php") )
    $padOptionsAppStart [$pad] [] = $padPrmName;

  $padPrm [$pad] [$padPrmName] = ( $padPrmValue === '' ) ? TRUE : padEval ( $padPrmValue );

  $padParmsSetType  = 'option';
  $padParmsSetName  = $padPrmName;
  $padParmsSetValue = $padPrm [$pad] [$padPrmName];

?>