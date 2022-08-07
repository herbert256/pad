<?php

  if ( $pSeq_loop == 1 ) return 1;
  if ( $pSeq_loop == 2 ) return 1; 
  if ( $pSeq_loop == 3 ) return 1; 

  return include PAD . "sequence/types/fibonacci/go.php"; 

?>