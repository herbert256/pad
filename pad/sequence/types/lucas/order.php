<?php

  if ( $padSeqLoop == 1 ) return 1;
  if ( $padSeqLoop == 2 ) return 3; 

  return include "sequence/types/fibonacci/go.php"; 

?>