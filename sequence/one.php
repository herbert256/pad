<?php

  if ( $pad_seq_unique ) {
    $pad_seq_unique_cnt++;
    if ( $pad_seq_unique_cnt > $pad_seq_unique)
      return false;
    if ( in_array ($pad_sequence, $pad_seq_base) )
      return true;
    $pad_seq_unique_cnt = 0;
  }

  $pad_seq_base [] = $pad_sequence;

  if ( $pad_seq_min and $pad_sequence < $pad_seq_min            ) goto skip;
  if ( $pad_seq_max and $pad_sequence > $pad_seq_max            ) goto skip;
  if ( $pad_seq_row and $pad_seq_row <> count($pad_seq_base)    ) goto skip;
  if ( $pad_seq_start and count($pad_seq_base) < $pad_seq_start ) goto skip;

  if ( count($pad_seq_multi_rows) and ! in_array (count($pad_seq_base), $pad_seq_multi_rows) )
    goto skip;

  foreach ( $pad_checks as $pad_check ) {
    pad_set_arr_var ( 'options_done', $pad_check, TRUE );
    if ( ! include PAD_HOME . "sequence/types/$pad_check/check.php" )     
      goto skip;
  }

  $pad_seq_result [] = $pad_sequence;

skip:

  if ( $pad_seq_row  and $pad_seq_row  == count($pad_seq_base)   ) return false;
  if ( $pad_seq_rows and $pad_seq_rows == count($pad_seq_result) ) return false;
  if ( $pad_seq_end  and $pad_seq_end  == count($pad_seq_base)   ) return false;
  if ( $pad_seq_max  and $pad_sequence > $pad_seq_max            ) return false;

  if ( count($pad_seq_multi_rows) and count($pad_seq_multi_rows) == count($pad_seq_result) )
    return false;
 
  return true;

?>