<?php

  $padPrmEval       = padEval ( $padPrmOne );

  $padOpt [$pad] [] = $padPrmEval;

  $padParmsSetType  = 'parm';
  $padParmsSetName  = array_key_last ( $padOpt [$pad] );
  $padParmsSetValue = $padPrmEval;

?>