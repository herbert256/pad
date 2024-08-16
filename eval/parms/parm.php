<?php

  if ($value)
    $tag = $value;
  elseif ( isset ( $parm [0] ) )
    $tag = $parm [0];
  else 
    $tag  = -1;

  if ( $value and isset ( $parm [0] ) )
    $padPrmTypeX = $parm [0];
  elseif ( isset ( $parm [1] ) and ! $value )
    $padPrmTypeX = $parm [1];
  else
    $padPrmTypeX = '';      

  if ( $padPrmTypeX )
    return padTagValue ( "$tag:$name" );
  else
    return padTagValue ( "$tag:$name#$padPrmTypeX" );
  
?>