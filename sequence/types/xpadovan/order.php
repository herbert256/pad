<?php

  if ( $pqLoop == 1 ) return 1;
  if ( $pqLoop == 2 ) return 1; 
  if ( $pqLoop == 3 ) return 1; 

  return include PQ . "types/fibonacci/go.php"; 

?>