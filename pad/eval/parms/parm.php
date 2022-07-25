<?php

  if ($value)
    $tag = $value;
  elseif ( isset ( $parm [0] ) )
    $tag = $parm [0];
  else 
    $tag  = -1;

  if ( $value and isset ( $parm [0] ) )
    $pad_parm_type = $parm [0];
  elseif ( isset ( $parm [1] ) and ! $value )
    $pad_parm_type = $parm [1];
  else
    $pad_parm_type = '';      

  $pad_result_parm = pad_field_tag ("$tag#$name#$pad_parm_type");

  if ( $pad_result_parm === PAD_NOT_FOUND )
    return NULL;
  else
    return $pad_result_parm;
  
?>