<?php
 
  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {
    $pad_walk = 'next';
    return TRUE;
  }

  $pad_fun [1] [0] = 'TYPE';
  $pad_fun [1] [1] = 'OPR';
  $pad_fun [1] [2] = $pad_tag_type;
  $pad_fun [1] [3] = $pad_tag;
  $pad_fun [1] [5] = 2 + count($pad_parms_val);

  foreach ( $pad_parms_val as $pad_k => $pad_v )
    $pad_fun [2+$pad_k] [0] = $pad_v;

  pad_eval_type (1, 0, $pad_fun, $pad_content, 1); 

  $pad_content = $pad_fun [1] [0];

?> 