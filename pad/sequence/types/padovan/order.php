<?php

  if ( $pad_sequence == 1 ) return 1;
  if ( $pad_sequence == 2 ) return 1; 
  if ( $pad_sequence == 3 ) return 1; 

  return include PAD_HOME . "pad/sequence/types/fibonacci/fibonacci.php"; 

?>