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
  $pad_seq_rows = 10;
  $pad_seq_parm = TRUE;

?>