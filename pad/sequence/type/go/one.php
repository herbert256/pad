<?php

  $pad_seq++;

  $pad_seq_protect_cnt++;
  if ( $pad_seq_protect_cnt > $pad_seq_protect )
    return FALSE;

  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/one.php" ) ) 
  
    $pad_sequence = include PAD . "sequence/types/$pad_seq_seq/one.php";

  elseif ( $pad_seq_build == 'fixed' ) 

    $pad_sequence = $pad_seq_loop;

  elseif ( $pad_seq_build == 'function' )

    $pad_sequence = "pad_sequence_$pad_seq_seq" ($pad_seq_loop);

  elseif ( $pad_seq_build == 'bool' )

    $pad_sequence = include "bool.php";

  elseif ( $pad_seq_random and pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/random.php") )

    $pad_sequence = include PAD . "sequence/types/$pad_seq_seq/random.php" ;

  else

    $pad_sequence = include PAD . "sequence/types/$pad_seq_seq/$pad_seq_build.php";

  if     ( $pad_sequence === NULL  ) return FALSE;
  elseif ( $pad_sequence === FALSE ) return TRUE;   
  elseif ( $pad_sequence === INF   ) return FALSE; 
  elseif ( $pad_sequence === NAN   ) return FALSE; 
  elseif ( $pad_sequence === TRUE  ) $pad_sequence = $pad_seq_loop;

  $pad_sequence = include 'operations.php';

  if     ( $pad_sequence === NULL  ) return FALSE;
  elseif ( $pad_sequence === FALSE ) return TRUE;   

  if ( $pad_seq_unique and in_array ($pad_sequence, $pad_seq_result) )
    return TRUE;

  if ( is_numeric($pad_sequence) and $pad_sequence > PHP_INT_MAX ) 
    return FALSE; 

  $pad_seq_base++;

  if ( is_numeric($pad_sequence) and $pad_sequence < $pad_seq_min ) return TRUE;  
  if ( is_numeric($pad_sequence) and $pad_sequence > $pad_seq_max ) return TRUE; 
  if ( $pad_seq_page  and $pad_seq_base < $pad_seq_page_start     ) return TRUE; 
  if ( $pad_seq_start and $pad_seq_base < $pad_seq_start          ) return TRUE;

  $pad_seq_result [] = $pad_sequence;

  $pad_seq_protect_cnt = 0;

  if ( $pad_seq_rows and count($pad_seq_result) >= $pad_seq_rows ) return FALSE;
  if ( $pad_seq_end  and $pad_seq_base          >= $pad_seq_end  ) return FALSE;

  return TRUE;

?>