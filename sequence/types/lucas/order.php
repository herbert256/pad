<?php

  if ( $padSeqLoop == 1 ) return 1;
  if ( $padSeqLoop == 2 ) return 3; 

  return include PAD . "sequence/types/fibonacci/go.php"; 

?>