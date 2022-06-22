<?php

  if ( $pad_seq_loop == 1 ) return 0;
  if ( $pad_seq_loop == 2 ) return 0; 
  if ( $pad_seq_loop == 3 ) return 1; 
 
  return $pad_seq_result [$pad_seq_loop - 2] +
         $pad_seq_result [$pad_seq_loop - 3] +
         $pad_seq_result [$pad_seq_loop - 4];

?>