<?php

  if ($value)
    $tag = $value;
  elseif ( isset ( $padarm [0] ) )
    $tag = $padarm [0];
  else 
    $tag  = -1;

  if ( $value and isset ( $padarm [0] ) )
    $padPrm [$pad]_type = $padarm [0];
  elseif ( isset ( $padarm [1] ) and ! $value )
    $padPrm [$pad]_type = $padarm [1];
  else
    $padPrm [$pad]_type = '';      

  $padResult_parm = pField_tag ("$tag#$name#$padPrm [$pad]_type");

  if ( $padResult_parm === INF )
    return NULL;
  else
    return $padResult_parm;
  
?>