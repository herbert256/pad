<?php

  if ( $padSeqLoop == 1 ) return 0;
  if ( $padSeqLoop == 2 ) return 1; 

  return include 'sequence/types/fibonacci/go.php'; 

?>