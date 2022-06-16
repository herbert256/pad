<?php

  $pad_seq_idx = $pad_seq_cnt;

  $pad_seq_cnt++;
  $pad_seq_protect_cnt++;

  if ( $pad_seq_cnt > $pad_seq_loop_max )
    { $pad_seq_sts_18++; return FALSE; }

  if ( $pad_seq_seq == 'random' or $pad_seq_random ) 
    $pad_sequence = pad_seq_random ( $pad_seq_loop_start, $pad_seq_loop_end );
  else
    $pad_sequence = $pad_seq_loop_idx;

  if ( $pad_seq_pull )   
     if ( isset ( $pad_seq_store [$pad_seq_pull] [$pad_seq_idx] ) ) 
       $pad_sequence = $pad_seq_store [$pad_seq_pull] [$pad_seq_idx];
     else 
      return FALSE;

  if ( $pad_seq_build == 'fixed' ) {  

    if ( ! $pad_seq_pull )  
      $pad_sequence = $pad_seq_loop_idx;

  } elseif ( isset ( $pad_seq_prepare [$pad_seq_idx] ) ) {

    if ( ! $pad_seq_pull ) 
      $pad_sequence = $pad_seq_prepare [$pad_seq_idx];

  } elseif ( $pad_seq_build == 'function' )

    $pad_sequence = "pad_sequence_$pad_seq_seq"($pad_sequence);

  elseif ( $pad_seq_build == 'bool' ) {

    $pad_sequence = include PAD . "sequence/type/go/bool.php";

    if ( $pad_sequence === NULL  ) return FALSE;
    if ( $pad_sequence === FALSE ) return TRUE; 

  } elseif ( $pad_seq_random and pad_file_exists(PAD . "sequence/types/$pad_seq_seq/random.php") )

    $pad_sequence = include PAD . "sequence/types/$pad_seq_seq/random.php" ;

  else

    $pad_sequence = include PAD . "sequence/types/$pad_seq_seq/$pad_seq_build.php";

  $pad_seq_unique_check = in_array ($pad_sequence, $pad_seq_base);

  $pad_seq_base [] = $pad_sequence;

  foreach ( $pad_seq_bool as $pad_seq_bool_name ) 
    if ( !  "pad_sequence_$pad_seq_bool_name"($pad_sequence) )
      return TRUE;

  if ( $pad_seq_build == 'jump' )
    if ( $pad_sequence !== NULL and $pad_sequence !== FALSE and $pad_sequence !== TRUE)  
      $pad_seq_loop_idx = $pad_sequence;

  if     ( $pad_sequence === NULL)  return FALSE;
  elseif ( $pad_sequence === FALSE) return TRUE; 
  elseif ( $pad_sequence === TRUE)  $pad_sequence = $pad_seq_loop_idx;

  if ( $pad_seq_max    and is_numeric($pad_sequence) and $pad_sequence > $pad_seq_max ) return FALSE; 
  if ( $pad_seq_min    and is_numeric($pad_sequence) and $pad_sequence < $pad_seq_min ) return TRUE;  
  
  if ( $pad_seq_start  and count($pad_seq_base) < $pad_seq_start               ) return TRUE;
  if ( $pad_seq_page   and count($pad_seq_base) < $pad_seq_page_start          ) return TRUE;
  if ( $pad_seq_row    and ! in_array (count($pad_seq_base), $pad_seq_row)     ) return TRUE;
  if ( $pad_seq_value  and ! in_array ($pad_sequence, $pad_seq_value)          ) return TRUE;
  if ( $pad_seq_unique and $pad_seq_unique_check                               ) return TRUE;
  if ( $pad_seq_protect_cnt > $pad_seq_protect                                 ) return FALSE;

  $pad_seq_result [] = $pad_sequence;

  $pad_seq_protect_cnt = 0;

  if ( $pad_seq_rows       and count($pad_seq_result) >= $pad_seq_rows         ) return FALSE;
  if ( $pad_seq_end        and count($pad_seq_base)   >= $pad_seq_end          ) return FALSE;
  if ( $pad_seq_row        and count($pad_seq_result) >= count($pad_seq_row)   ) return FALSE;
  if ( $pad_seq_value      and count($pad_seq_result) >= count($pad_seq_value) ) return FALSE;
  if ( $pad_seq_loop_end   and $pad_seq_cnt           >= $pad_seq_loop_end     ) return FALSE;

  return TRUE;

?>  