<?php

  $pad_seq_seq  = 'loop';
  $pad_seq_set  = 'sequence';
  $pad_seq_parm = TRUE;

  $pad_seq_tmp = $pad_parm;
  if ( $pad_tag == 'sequence' and pad_valid($pad_seq_tmp) and isset($pad_sequence_store [$pad_seq_tmp]) )
    return include 'sequence/store.php';
  
  $pad_seq_tmp = $pad_tag;
  if ( pad_valid($pad_seq_tmp) and isset($pad_sequence_store [$pad_seq_tmp]) )
    return include 'sequence/store.php';

  $pad_seq_tmp = $pad_tag;
  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_tmp" ) )  
    return include 'sequence/type.php';

  $pad_seq_tmp = $pad_parms_tag['type'] ?? '';
  if ( pad_valid($pad_seq_tmp) and pad_file_exists ( PAD . "sequence/types/$pad_seq_tmp" ) )  
    return include 'sequence/type.php';

  $pad_seq_tmp = array_key_first($pad_parms_tag) ?? '';
  if ( pad_valid($pad_seq_tmp) and pad_file_exists ( PAD . "sequence/types/$pad_seq_tmp" ) )  
    return include 'sequence/type.php';

  $pad_seq_tmp = $pad_parm ?? '';
  if ( pad_valid($pad_seq_tmp) and pad_file_exists ( PAD . "sequence/types/$pad_seq_tmp" ) )  
    return include 'sequence/type.php';

  $pad_seq_tmp = $pad_tag ?? '';
  if ( pad_file_exists ( PAD . "sequence/actions/$pad_seq_tmp.php" ) )
    return include 'sequence/action.php';

  $pad_seq_tmp = $pad_parms_tag['type'] ?? '';
  if ( pad_valid($pad_seq_tmp) and pad_file_exists ( PAD . "sequence/actions/$pad_seq_tmp.php" ) )
    return include 'sequence/action.php';

  $pad_seq_tmp = $pad_parm ?? '';
  if ( pad_valid($pad_seq_tmp) and pad_file_exists ( PAD . "sequence/actions/$pad_seq_tmp.php" ) )
   return include 'sequence/action.php';

  $pad_seq_tmp = array_key_first($pad_parms_tag) ?? '';
  if ( $pad_parm == '' and $pad_tag == 'sequence' and pad_valid($pad_seq_tmp) and pad_file_exists ( PAD . "sequence/actions/$pad_seq_tmp.php" ) )
    return include 'sequence/action.php';

  $pad_seq_tmp = array_key_first($pad_parms_tag) ?? '';
  if ( pad_valid($pad_seq_tmp) and isset($pad_sequence_store [$pad_seq_tmp]) )
    return include 'sequence/store.php';
 
  if ( strpos($pad_parm, '..') ) {
    $pad_seq_parm = $pad_parm;
    return include 'sequence/range.php';
  } 

  if ( ctype_digit($pad_parm) ) {
    $pad_seq_parm = "1..$pad_parm";
    return include 'sequence/range.php';
  } 

?>