<?php

  if ( is_null($pad_sequence) or $pad_sequence === FALSE )
    return false;

  if ( $pad_seq_to and $pad_sequence > $pad_seq_to ) 
    return false;

  if ( $pad_seq_unique ) {
    $pad_seq_unique_cnt++;
    if ( $pad_seq_unique_cnt > $pad_seq_unique)
      return false;
    if ( in_array ($pad_sequence, $pad_seq_base) )
      return true;
    $pad_seq_unique_cnt = 0;
  }

  $pad_seq_base [] = $pad_sequence;

  if ( $pad_seq_from and $pad_sequence < $pad_seq_from          ) goto skip;
  if ( $pad_seq_start and count($pad_seq_base) < $pad_seq_start ) goto skip;

  if ( count($pad_seq_value) and ! in_array ($pad_sequence, $pad_seq_value) )
    goto skip;

  if ( count($pad_seq_row) and ! in_array (count($pad_seq_base), $pad_seq_row) )
    goto skip;

  foreach ( $pad_seq_checks as $pad_seq_check ) {
    if ( ! include PAD_HOME . "sequence/checks/$pad_seq_check.php" )     
      goto skip;
  }

  $pad_sequence_result = $pad_sequence;
  foreach ( $pad_seq_actions as $pad_seq_action ) 
    $pad_sequence_result = include PAD_HOME . "sequence/actions/$pad_seq_action.php"; 

  $pad_seq_result [] = $pad_sequence_result;
  $pad_seq_skip_cnt = 0;
  goto go;

skip: 

  $pad_seq_skip_cnt++;
  if ( $pad_seq_skip_cnt >  $pad_seq_skip )
    return false;

go:
  if ( $pad_seq_rows and $pad_seq_rows == count($pad_seq_result) ) return false;
  if ( $pad_seq_end  and $pad_seq_end  == count($pad_seq_base)   ) return false;

  if ( count($pad_seq_value) and count($pad_seq_value) == count($pad_seq_result) )
    return false;

  if ( count($pad_seq_row) and count($pad_seq_row) == count($pad_seq_result) )
    return false;

  return true;

?>