<?php

  if ( isset( $pad_sw_now[$pad_parms]) )
    $pad_sw_now  [$pad_parms]++;
  else {
    $pad_sw_now  [$pad_parms] = 0;
    $pad_sw_vars [$pad_parms] = array_values($pad_parms_seq);
  }
   
  return $pad_sw_vars [$pad_parms] [ $pad_sw_now [$pad_parms] % count($pad_parms_seq) ];

?>