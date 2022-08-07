<?php

  if ( $pSeq_loop == 1 ) return 0;
  if ( $pSeq_loop == 2 ) return 0; 
  if ( $pSeq_loop == 3 ) return 1; 
 
  return $pSeq_result [$pSeq_loop - 2] +
         $pSeq_result [$pSeq_loop - 3] +
         $pSeq_result [$pSeq_loop - 4];

?>