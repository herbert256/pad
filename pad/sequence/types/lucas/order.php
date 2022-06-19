<?php

  if ( $pad_seq_now == 1 ) return 1;
  if ( $pad_seq_now == 2 ) return 3; 

  return include PAD . "sequence/types/fibonacci/fibonacci.php"; 

?>