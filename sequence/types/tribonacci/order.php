<?php

  if ( $pad_sequence == 1 ) return 0;
  if ( $pad_sequence == 2 ) return 0; 
  if ( $pad_sequence == 3 ) return 1; 
 
  return $pad_seq_base [$pad_sequence - 2] +
         $pad_seq_base [$pad_sequence - 3] +
         $pad_seq_base [$pad_sequence - 4];

?>