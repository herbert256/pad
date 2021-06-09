<?php

  if ( $pad_seq_min and $pad_seq_now < $pad_seq_min )
    return true;

  if ( $pad_seq_max and $pad_seq_now > $pad_seq_max )  
    return true;
      
  if ( $pad_seq_row and $pad_seq_row <> count($pad_seq_base)+1 )
    return true;
      
  if ( $pad_seq_start and count($pad_seq_base)+1 < $pad_seq_start )
    return true;

  return false; 

?>