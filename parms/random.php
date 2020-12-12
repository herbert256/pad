<?php

  if ( $pad_parms_tag ['random'] === TRUE)
    $pad_rnd_random = 1;
  else 
    $pad_rnd_random = $pad_parms_tag ['random'];

  $pad_rnd_count = count($pad_data[$pad_lvl]);

  $pad_rnd_temp = [];

  for ($pad_rnd_i=1; $pad_rnd_i <= $pad_rnd_random; $pad_rnd_i++) { 

    if ( ! count($pad_data[$pad_lvl]) ) 
      break;

    $pad_rnd_rand = rand (1, count($pad_data[$pad_lvl]));

    $pad_rnd_now = 0;
    foreach ( $pad_data[$pad_lvl] as $pad_rnd_key => $pad_rnd_value ) {
      $pad_rnd_now++;
      if ( H_now == $pad_rnd_rand ) {
        $pad_rnd_temp [$pad_rnd_key] = [$pad_rnd_value];
        unset $pad_data[$pad_lvl] [$pad_rnd_key];
        break;
      }
    }
      
  }

  $pad_data[$pad_lvl] = $pad_rnd_temp;

?>