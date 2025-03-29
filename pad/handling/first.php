<?php

  if ( count($padData [$pad]) > $padHandCnt )
    if ( $padHandName == 'first')
      $padData [$pad] = array_slice ( $padData [$pad], 0, $padHandCnt );
    else 
      $padData [$pad] = array_slice ( $padData [$pad], $padHandCnt * -1 );
  
?>