<?php

  if ( $padSeqLoop == 1 ) return 1;
  if ( $padSeqLoop == 2 ) return 3; 

  return include "$padSeqTypes/fibonacci/go.php"; 

?>