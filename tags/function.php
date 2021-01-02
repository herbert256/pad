<?php

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pad_tag_save      = $pad_tag;
  $pad_tag_type_save = $pad_tag_type;

  if ( $pad_tag == 'function' ) {

    $pad_tag = $pad_parm;

    if ( isset ($pad_parms_tag ['type'] ) )                     
      $pad_tag_type = $pad_parms_tag ['type'];                
    else
      $pad_tag_type = pad_function_type ($pad_tag);

    unset ( $pad_parms_val [ array_key_first ($pad_parms_val) ] );
    unset ( $pad_parms_seq [ array_key_first ($pad_parms_seq) ] );
    unset ( $pad_parms_org [ array_key_first ($pad_parms_org) ] );

  else {

    $pad_tag = $pad_parms_tag ['type'];
 
    $pad_tag_type = pad_function_type ($pad_tag);

    $pad_parms_org = [];
    $pad_parms_seq = [];
    $pad_parms_tag = [];
    $pad_parms_val = [];   

  }

  $pad_tag_type = "function_$pad_tag_type";

  $pad_function_result = include PAD_HOME . 'types/$pad_tag_type.php';

  $pad_tag      = $pad_tag_save;
  $pad_tag_type = $pad_tag_type_save;

  return $pad_function_result;

?>