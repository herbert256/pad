<?php

  if ( $padNull [$pad] )

    $padNow = [];

  elseif ( $padElse [$pad] )

    if     ( $padArray [$pad]        ) $padNow = array_slice ($padTagResult, 0, 1); 
    elseif ( count ($padData [$pad]) ) $padNow = array_slice ($padData [$pad], 0, 1); 
    else                           $padNow = padDefaultData ();  

  elseif ( $padArray [$pad] )

    $padNow = $padTagResult;

  else 

    $padNow = $padData [$pad];

  $padData [$pad] = padMakeData ( $padNow );   

  $padDefault [$pad] = padIsDefaultData ( $padData [$pad] );

?>