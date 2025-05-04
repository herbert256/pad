<?php

  if ( $pqParm and is_numeric ( $pqParm ) )
    if  ( mt_rand ( 1, $pqParm ) == 1 ) return TRUE;
    else                                return FALSE;

  if ( is_array ( $pqFixed ) and count ( $pqFixed ) )
     return $pqFixed [ array_rand ( $pqFixed, 1) ];

  if ( $pqInc <> 1 ) {
  
    $pqRandomRand = rand ( 0, $pqRandomSteps ) * $pqInc;

    return $pqRandomStart + $pqRandomRand;

  }

  return rand ( $pqRandomStart, $pqRandomEnd );

?>