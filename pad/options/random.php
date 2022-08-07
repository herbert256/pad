<?php

  $pRnd_random = ( $pPrms_tag ['random'] === TRUE) ? 1 : $pPrms_tag ['random'];

  $pRnd_temp = [];

  for ($pRnd_i=1; $pRnd_i <= $pRnd_random; $pRnd_i++) { 

    if ( ! count($pData[$pad]) ) 
      break;

    $pRnd_rand = rand (1, count($pData[$pad]));

    $pRnd_now = 0;
    foreach ( $pData[$pad] as $pRnd_key => $pRnd_value ) {
      $pRnd_now++;
      if ( $pRnd_now == $pRnd_rand ) {
        $pRnd_temp [$pRnd_key] = $pData [$pad] [$pRnd_key] ;
        unset ( $pData [$pad] [$pRnd_key] );
        break;
      }
    }
      
  }

  $pData [$pad] = $pRnd_temp;

?>