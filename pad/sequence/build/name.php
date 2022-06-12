<?php

  if ( ! $pad_seq_name and isset($pad_parms_tag ['toData'] ) )
    $pad_seq_name = $pad_parms_tag ['toData']; 

  if ( ! $pad_seq_name )
    $pad_seq_name = $pad_seq_seq; 

  $pad_name = $pad_parameters [$pad_lvl] ['name'] = $pad_seq_name;

?>