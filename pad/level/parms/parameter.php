<?php
  
  $padPrmEval = padEval ( $padPrmOne );

  $padOpt [$pad] [] = $padPrmEval;

  $padParmsSet = $padPrmEval;

  if ( ! $padFirstParm [$pad] )
    $padFirstParm [$pad] = $padPrmEval;
    
?>