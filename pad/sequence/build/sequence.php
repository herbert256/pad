<?php

  if ( file_exists ( PAD . "sequence/types/$pad_tag" ) ) {  

    $pad_seq_seq = $pad_tag;
    $pad_seq_set = $pad_tag;

    if ( isset($pad_parms_tag[$pad_seq_seq]) )
      $pad_seq_parm = $pad_parms_tag[$pad_seq_seq];
    else
      $pad_seq_parm = $pad_parm;

    return;
 
  } 

  if ( file_exists ( PAD . "sequence/actions/$pad_tag" ) ) {  

    if ( isset($pad_parms_tag[$pad_tag]) )
      $pad_seq_parm = $pad_parms_tag[$pad_tag];
    else
      $pad_seq_parm = $pad_parm;

    $pad_seq_parms = pad_explode($pad_parm, '|');

    $pad_seq_seq  = 'pull';
    $pad_seq_pull = $pad_seq_parms[0];
    $pad_seq_set  = $pad_tag;

    if ( count(var) > 1 ) {
      $pad_seq_parms[0];
      $pad_parms_tag [$pad_tag] = implode('|', $pad_seq_parms);
      $pad_parameters [$pad_lvl] ['parms_tag'] [$pad_tag] = $pad_parms_tag [$pad_tag];
    }

    return;
 
  } 

  $pad_seq_tmp = $pad_parms_tag['type'] ?? '';

  if ( pad_valid($pad_seq_tmp) and file_exists(PAD . "sequence/types/$pad_seq_tmp") ) {

    $pad_seq_seq = $pad_seq_tmp;
    $pad_seq_set = $pad_seq_tmp;

    if ( isset($pad_parms_tag[$pad_seq_seq]) )
      $pad_seq_parm = $pad_parms_tag[$pad_seq_seq];
    else
      $pad_seq_parm = $pad_parm;

    return;
 
  } 

  $pad_seq_tmp = array_key_first($pad_parms_tag) ?? '';

  if ( pad_valid($pad_seq_tmp) and file_exists(PAD . "sequence/types/$pad_seq_tmp") ) {

    $pad_seq_seq = $pad_seq_tmp;
    $pad_seq_set = $pad_seq_tmp;

    if ( isset($pad_parms_tag[$pad_seq_seq]) )
      $pad_seq_parm = $pad_parms_tag[$pad_seq_seq];
    elseif ( isset($pad_parms_seq[1]) )
      $pad_seq_parm = $pad_parms_seq[1];
    else
      $pad_seq_parm = '';

    return;

  } 

  if ( pad_valid($pad_parm) and file_exists(PAD . "sequence/types/$pad_parm") ) {

    $pad_seq_seq  = $pad_parm;
    $pad_seq_set  = $pad_parm;
    $pad_seq_parm = TRUE;

    return;

  } 

  $pad_seq_tmp = $pad_tag;

  if ( pad_valid($pad_seq_tmp) and isset($pad_seq_store [$pad_seq_tmp]) ) {

    $pad_seq_seq  = 'pull';
    $pad_seq_pull = $pad_seq_tmp;
    $pad_seq_set  = $pad_seq_tmp;
    $pad_seq_parm = $pad_seq_tmp;

    return;

  } 

  if ( pad_valid($pad_parm) and isset($pad_seq_store [$pad_parm]) ) {

    $pad_seq_seq  = 'pull';
    $pad_seq_pull = $pad_parm;
    $pad_seq_set  = $pad_parm;
    $pad_seq_parm = $pad_parm;

    return;

  } 

  $pad_seq_tmp = array_key_first($pad_parms_tag) ?? '';

  if ( pad_valid($pad_seq_tmp) and isset($pad_seq_store [$pad_seq_tmp]) ) {

    $pad_seq_seq  = 'pull';
    $pad_seq_pull = $pad_seq_tmp;
    $pad_seq_set  = $pad_seq_tmp;
    $pad_seq_parm = $pad_seq_tmp;

    return;

  } 

  if ( strpos($pad_parm, '..') ) {

    $pad_seq_seq  = 'range';
    $pad_seq_set  = 'sequence';
    $pad_seq_parm = $pad_parm;

    return;

  } 

  if ( ctype_digit($pad_parm) ) {

    $pad_seq_seq  = 'range';
    $pad_seq_set  = 'sequence';
    $pad_seq_parm = "1..$pad_parm";

    return;

  } 

  if ( isset($pad_parms_tag['rows']) ) {

    $pad_seq_seq  = 'range';
    $pad_seq_set  = 'sequence';
    $pad_seq_parm = "1.." . $pad_parms_tag['rows'];

    return;

  } 

  $pad_seq_seq  = 'loop';
  $pad_seq_set  = 'sequence'; 
  $pad_seq_parm = TRUE;

?>