<?php

  if ( isset($pPrmsTag[$p] ['from']) )
    $pSeq_loop_start = $pSeq_from;
  else
    $pSeq_loop_start = 1; 
  
  if ( isset($pPrmsTag[$p] ['to']) )
    $pSeq_loop_end = $pSeq_to;
  else 
    $pSeq_loop_end = PHP_INT_MAX;

?>