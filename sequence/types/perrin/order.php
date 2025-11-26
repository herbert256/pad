<?php

  if ( $pqLoop == 1 ) return 3;
  if ( $pqLoop == 2 ) return 0; 
  if ( $pqLoop == 3 ) return 2; 

  return include "sequence/types/fibonacci/go.php"; 

?>