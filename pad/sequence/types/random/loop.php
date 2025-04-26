<?php

  if ( is_array ( $pqFixed ) and count ( $pqFixed ) )
     return $pqFixed [ array_rand ( $pqFixed, 1) ];

  if ( $pqRandomStep <> 1 ) {
  
    $pqRandomRand = rand ( 0, $pqRandomSteps ) * $pqRandomStep;

    return  $pqRandomStart + $pqRandomRand;

  }


  return rand ( $pqRandomStart, $pqRandomEnd );

?>