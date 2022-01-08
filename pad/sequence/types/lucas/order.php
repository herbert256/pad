<?php

  if ( $pad_sequence == 1 ) return 1;
  if ( $pad_sequence == 2 ) return 3; 

  return include PAD . "sequence/types/fibonacci/fibonacci.php"; 

?>