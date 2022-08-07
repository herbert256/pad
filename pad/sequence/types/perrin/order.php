<?php

  if ( $pSeq_loop == 1 ) return 3;
  if ( $pSeq_loop == 2 ) return 0; 
  if ( $pSeq_loop == 3 ) return 2; 

  return include PAD . "sequence/types/fibonacci/go.php"; 

?>