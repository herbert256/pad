<?php

  if ( $padNull [$pad] )

    $padData [$pad] = [];

  elseif ( $padElse [$pad] )

    if     ( $padArray [$pad]        ) $padData [$pad] = array_slice ($padTagResult, 0, 1); 
    elseif ( count ($padData [$pad]) ) $padData [$pad] = array_slice ($padData [$pad], 0, 1); 
    else                               $padData [$pad] = padDefaultData ();  

  elseif ( $padArray [$pad] )

    $padData [$pad] = $padTagResult;

  $padData [$pad]  = padData ( $padData [$pad] );   
  $padCount [$pad] = count ( $padData [$pad] );

  $padDefault [$pad] = padIsDefaultData ( $padData [$pad] );

?>