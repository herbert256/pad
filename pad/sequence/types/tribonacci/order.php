<?php

  if ( $padSeq_loop == 1 ) return 0;
  if ( $padSeq_loop == 2 ) return 0; 
  if ( $padSeq_loop == 3 ) return 1; 
 
  return $padSeq_result [$padSeq_loop - 2] +
         $padSeq_result [$padSeq_loop - 3] +
         $padSeq_result [$padSeq_loop - 4];

?>