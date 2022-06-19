<?php

  if ( $pad_seq_loop == 1 ) return 3;
  if ( $pad_seq_loop == 2 ) return 0; 
  if ( $pad_seq_loop == 3 ) return 2; 

  return include PAD . "sequence/types/fibonacci/fibonacci.php"; 

?>