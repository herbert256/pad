<?php

  if ( isset($padPrmsTag [$pad] ['from']) )
    $padSeq_loop_start = $padSeq_from;
  else
    $padSeq_loop_start = 1; 
  
  if ( isset($padPrmsTag [$pad] ['to']) )
    $padSeq_loop_end = $padSeq_to;
  else 
    $padSeq_loop_end = PHP_INT_MAX;

?>