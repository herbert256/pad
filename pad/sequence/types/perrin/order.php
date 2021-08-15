<?php

  if ( $pad_sequence == 1 ) return 3;
  if ( $pad_sequence == 2 ) return 0; 
  if ( $pad_sequence == 3 ) return 2; 

  return include PAD_HOME . "pad/sequence/types/fibonacci/fibonacci.php"; 

?>