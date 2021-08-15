<?php

  $parm = $parm;

  if ($value)
    $tag = $value;
  elseif ( isset ( $parm [0] ) )
    $tag = $parm [0];
  else 
    $tag  = -1;

  if ( $value and isset ( $parm [0] ) )
    $type = $parm [0];
  elseif ( isset ( $parm [1] ) and ! $value )
    $type = $parm [1];
  else
    $type = '';      

  $result = pad_field_tag ("$tag#$name#$type");

  if ( $result === PAD_NOT_FOUND )
    return NULL;
  else
    return $result;
  
?>