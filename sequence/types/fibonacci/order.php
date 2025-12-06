<?php

  if ( $pqLoop == 1 ) return 0;
  if ( $pqLoop == 2 ) return 1; 

  return include PQ . 'types/fibonacci/go.php'; 

?>