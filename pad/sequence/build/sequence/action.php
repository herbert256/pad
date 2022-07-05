<?php

  if ( isset($pad_parms_tag[$pad_seq_tmp]) )
    $pad_seq_parm = $pad_parms_tag[$pad_seq_tmp];
  else
    $pad_seq_parm = $pad_parm;

  $pad_seq_parms = pad_explode($pad_seq_parm, '|');

  $pad_seq_seq  = 'pull';
  $pad_seq_pull = $pad_seq_parms[0]; 
  $pad_seq_set  = $pad_seq_tmp;

  unset ( $pad_seq_parms[0] );

  if ( count($pad_seq_parms) )
    $pad_parms_tag [$pad_seq_tmp] = implode('|', $pad_seq_parms);
  else
    $pad_parms_tag [$pad_seq_tmp] = true;

  $pad_parameters [$pad_lvl] ['parms_tag'] [$pad_seq_tmp] = $pad_parms_tag [$pad_seq_tmp];

?>