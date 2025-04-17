<?php

  if ( $pqRandomStep <> 1 ) {
  
    $pqRandomRand = rand ( 0, $pqRandomSteps ) * $pqRandomStep;

    return  $pqRandomStart + $pqRandomRand;

  }

  return rand ( $pqRandomStart, $pqRandomEnd );

?>