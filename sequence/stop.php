<?php

  if ( $pad_seq_row  and $pad_seq_row  == count($pad_seq_base) )
    return true;
  
  if ( $pad_seq_rows and $pad_seq_rows == count($pad_seq_result) )
    return true;
      
  if ( $pad_seq_end  and $pad_seq_end  == count($pad_seq_base) ) 
    return true;

  return false; 

?>