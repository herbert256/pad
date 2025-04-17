<?php

  if ( $pqLoop == 1 ) return 0;
  if ( $pqLoop == 2 ) return 0; 
  if ( $pqLoop == 3 ) return 1; 
 
  return $pqResult [$pqLoop - 2] +
         $pqResult [$pqLoop - 3] +
         $pqResult [$pqLoop - 4];

?>