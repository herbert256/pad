<?php

  $pad_seq++;

  $pad_seq_protect_cnt++;
  if ( $pad_seq_protect_cnt > $pad_seq_protect )
    pad_error ("No stop limit in the sequence tag"); 

  if ( $pad_seq_seq == 'make' ) {

    foreach ( $pad_seq_make as $pad_seq_make_name )
      include PAD . "sequence/types/$pad_seq_make_name/make.php"; 

    $pad_sequence = $pad_seq_loop;

  } elseif ( $pad_seq_build == 'fixed' ) 

    $pad_sequence = $pad_seq_loop;

  elseif ( $pad_seq_build == 'function' )

    $pad_sequence = "pad_sequence_$pad_seq_seq"($pad_seq_loop);

  elseif ( $pad_seq_build == 'bool' )

    $pad_sequence = include "bool.php";

  elseif ( $pad_seq_random and pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/random.php") )

    $pad_sequence = include PAD . "sequence/types/$pad_seq_seq/random.php" ;

  else

    $pad_sequence = include PAD . "sequence/types/$pad_seq_seq/$pad_seq_build.php";

  if     ( $pad_sequence === NULL  ) return FALSE;
  elseif ( $pad_sequence === FALSE ) return TRUE;   
  elseif ( $pad_sequence === INF   ) $pad_sequence = '?';
  elseif ( $pad_sequence === NAN   ) $pad_sequence = '?';
  elseif ( $pad_sequence === TRUE  ) $pad_sequence = $pad_seq_loop;

  foreach ( $pad_seq_bool as $pad_seq_bool_name ) 
    if ( ! "pad_sequence_bool_$pad_seq_bool_name"($pad_sequence) )
      return TRUE;

  if ( is_numeric($pad_sequence) and $pad_sequence < $pad_seq_min ) return TRUE;  
  if ( is_numeric($pad_sequence) and $pad_sequence > $pad_seq_max ) return TRUE; 
  
  if ( $pad_seq_unique and in_array ($pad_sequence, $pad_seq_result) ) 
    return TRUE;

  $pad_seq_result [] = $pad_sequence;

  $pad_seq_protect_cnt = 0;

  if ( $pad_seq_rows and count($pad_seq_result) >= $pad_seq_rows ) 
    return FALSE;

  return TRUE;

?>