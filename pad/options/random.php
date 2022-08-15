<?php

  $padRnd_random = ( $padPrmsTag [$pad] ['random'] === TRUE) ? 1 : $padPrmsTag [$pad] ['random'];

  $padRnd_temp = [];

  for ($padRnd_i=1; $padRnd_i <= $padRnd_random; $padRnd_i++) { 

    if ( ! count($padData [$pad]) ) 
      break;

    $padRnd_rand = rand (1, count($padData [$pad]));

    $padRnd_now = 0;
    foreach ( $padData [$pad] as $padRnd_key => $padRnd_value ) {
      $padRnd_now++;
      if ( $padRnd_now == $padRnd_rand ) {
        $padRnd_temp [$padRnd_key] = $padData [$pad] [$padRnd_key] ;
        unset ( $padData [$pad] [$padRnd_key] );
        break;
      }
    }
      
  }

  $padData [$pad] = $padRnd_temp;

?>