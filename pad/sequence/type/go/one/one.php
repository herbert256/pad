<?php

  $pad_seq_cnt++;
  $pad_seq_protect_cnt++;

  if ( $pad_seq_cnt         > $pad_seq_max ) return FALSE;
  if ( $pad_seq_protect_cnt > $pad_seq_protect  ) return FALSE;

  if ( $pad_seq_build == 'fixed' ) 

    $pad_seq_now = $pad_seq_loop;

  elseif ( $pad_seq_build == 'function' )

    $pad_seq_now = "pad_seq_now_$pad_seq_seq"($pad_seq_loop);

  elseif ( $pad_seq_build == 'bool' )

    $pad_seq_now = include "bool.php";

  elseif ( $pad_seq_random and pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/random.php") )

    $pad_seq_now = include PAD . "sequence/types/$pad_seq_seq/random.php" ;

  else

    $pad_seq_now = include PAD . "sequence/types/$pad_seq_seq/$pad_seq_build.php";

  if     ( $pad_seq_now === NULL)  return FALSE;
  elseif ( $pad_seq_now === FALSE) return TRUE; 
  elseif ( $pad_seq_now === TRUE)  $pad_seq_now = $pad_seq_loop;

  foreach ( $pad_seq_bool as $pad_seq_bool_name ) 
    if ( ! "pad_seq_now_bool_$pad_seq_bool_name"($pad_seq_now) )
      return TRUE;

  $pad_seq_unique_check = in_array ($pad_seq_now, $pad_seq_base);

  $pad_seq_base [] = $pad_seq_now;

  if ( is_numeric($pad_seq_now) and $pad_seq_now > $pad_seq_max ) return FALSE; 
  if ( is_numeric($pad_seq_now) and $pad_seq_now < $pad_seq_min ) return TRUE;  
  
  if ( $pad_seq_page   and count($pad_seq_base) < $pad_seq_page_start          ) return TRUE;
  if ( $pad_seq_row    and ! in_array (count($pad_seq_base), $pad_seq_row)     ) return TRUE;
  if ( $pad_seq_value  and ! in_array ($pad_seq_now, $pad_seq_value)           ) return TRUE;
  if ( $pad_seq_unique and $pad_seq_unique_check                               ) return TRUE;

  $pad_seq_result [] = $pad_seq_now;

  $pad_seq_protect_cnt = 0;

  if ( $pad_seq_rows       and count($pad_seq_result) >= $pad_seq_rows         ) return FALSE;
  if ( $pad_seq_row        and count($pad_seq_result) >= count($pad_seq_row)   ) return FALSE;
  if ( $pad_seq_value      and count($pad_seq_result) >= count($pad_seq_value) ) return FALSE;

  return TRUE;

?>  