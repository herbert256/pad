<?php

  if ($value)
    $tag = $value;
  elseif ( isset ( $parm [0] ) )
    $tag = $parm [0];
  else 
    $tag  = -1;

  if ( $value and isset ( $parm [0] ) )
    $pPrm [$p]_type = $parm [0];
  elseif ( isset ( $parm [1] ) and ! $value )
    $pPrm [$p]_type = $parm [1];
  else
    $pPrm [$p]_type = '';      

  $pResult_parm = pField_tag ("$tag#$name#$pPrm [$p]_type");

  if ( $pResult_parm === INF )
    return NULL;
  else
    return $pResult_parm;
  
?>