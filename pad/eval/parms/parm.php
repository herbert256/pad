<?php

  if ($value)
    $tag = $value;
  elseif ( isset ( $parm [0] ) )
    $tag = $parm [0];
  else 
    $tag  = -1;

  if ( $value and isset ( $parm [0] ) )
    $padPrmType = $parm [0];
  elseif ( isset ( $parm [1] ) and ! $value )
    $padPrmType = $parm [1];
  else
    $padPrmType = '';      

  $padResultParm = padFieldTag ("$tag#$name#$padPrmType");

  if ( $padResultParm === INF )
    return NULL;
  else
    return $padResultParm;
  
?>