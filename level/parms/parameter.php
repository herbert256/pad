<?php
  
  $padPrmEval = padEval ( $padPrmOne );

  $padOpt [$pad] [] = $padPrmEval;

  if ( ! $padFirstParm [$pad] )
    $padFirstParm [$pad] = $padPrmEval;
    
?>