<?php

  if ( $pqLoop == 1 ) return 3;
  if ( $pqLoop == 2 ) return 0; 
  if ( $pqLoop == 3 ) return 2; 

  return include PT . "fibonacci/go.php"; 

?>