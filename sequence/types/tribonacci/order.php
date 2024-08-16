<?php

  if ( $padSeqLoop == 1 ) return 0;
  if ( $padSeqLoop == 2 ) return 0; 
  if ( $padSeqLoop == 3 ) return 1; 
 
  return $padSeqResult [$padSeqLoop - 2] +
         $padSeqResult [$padSeqLoop - 3] +
         $padSeqResult [$padSeqLoop - 4];

?>