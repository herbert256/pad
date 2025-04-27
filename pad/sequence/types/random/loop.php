<?php

  if ( is_array ( $pqFixed ) and count ( $pqFixed ) )
     return $pqFixed [ array_rand ( $pqFixed, 1) ];

  if ( $pqInc <> 1 ) {
  
    $pqRandomRand = rand ( 0, $pqRandomSteps ) * $pqInc;

    return $pqRandomStart + $pqRandomRand;

  }


  return rand ( $pqRandomStart, $pqRandomEnd );

?>