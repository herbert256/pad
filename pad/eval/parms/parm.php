<?php

  if ($value)
    $tag = $value;
  elseif ( isset ( $parm [0] ) )
    $tag = $parm [0];
  else 
    $tag  = -1;

  if ( $value and isset ( $parm [0] ) )
    $padPrm [$pad]_type = $parm [0];
  elseif ( isset ( $parm [1] ) and ! $value )
    $padPrm [$pad]_type = $parm [1];
  else
    $padPrm [$pad]_type = '';      

  $padResultParm = padFieldTag ("$tag#$name#$padPrm [$pad]_type");

  if ( $padResultParm === INF )
    return NULL;
  else
    return $padResultParm;
  
?>