<?php

  $pad_rnd_random = ( $pad_prms_tag ['random'] === TRUE) ? 1 : $pad_prms_tag ['random'];

  $pad_rnd_temp = [];

  for ($pad_rnd_i=1; $pad_rnd_i <= $pad_rnd_random; $pad_rnd_i++) { 

    if ( ! count($pad_data[$pad]) ) 
      break;

    $pad_rnd_rand = rand (1, count($pad_data[$pad]));

    $pad_rnd_now = 0;
    foreach ( $pad_data[$pad] as $pad_rnd_key => $pad_rnd_value ) {
      $pad_rnd_now++;
      if ( $pad_rnd_now == $pad_rnd_rand ) {
        $pad_rnd_temp [$pad_rnd_key] = $pad_data [$pad] [$pad_rnd_key] ;
        unset ( $pad_data [$pad] [$pad_rnd_key] );
        break;
      }
    }
      
  }

  $pad_data [$pad] = $pad_rnd_temp;

?>