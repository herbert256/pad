<?php

  if ( $padSeqLoop == 1 ) return 3;
  if ( $padSeqLoop == 2 ) return 0; 
  if ( $padSeqLoop == 3 ) return 2; 

  return include "$padSeqTypes/fibonacci/go.php"; 

?>