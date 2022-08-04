<?php

  if ( isset( $pad_sw_now[$pad_prms]) )
    $pad_sw_now  [$pad_prms]++;
  else {
    $pad_sw_now  [$pad_prms] = 0;
    $pad_sw_vars [$pad_prms] = array_values($pad_prms_val);
  }
   
  return $pad_sw_vars [$pad_prms] [ $pad_sw_now [$pad_prms] % count($pad_prms_val) ];

?>