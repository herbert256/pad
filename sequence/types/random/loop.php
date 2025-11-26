<?php

  if ( $pqParm ) {

    if ( str_contains ( $pqParm, '%' ) ) {
      $pqRandomPerc = str_replace ('%', '', $pqParm); 
      if  ( mt_rand ( 1, 100 ) <= $pqRandomPerc ) return TRUE;
      else                                      return FALSE;
    }

    if ( is_numeric ( $pqParm ) )
      if  ( mt_rand ( 1, $pqParm ) == 1 ) return TRUE;
      else                                return FALSE;

  }


  if ( is_array ( $pqFixed ) and count ( $pqFixed ) )
     return $pqFixed [ array_rand ( $pqFixed, 1) ];

  if ( $pqInc <> 1 ) {  
    $pqRandomRand = rand ( 0, $pqRandomSteps ) * $pqInc;
    return $pqRandomStart + $pqRandomRand;
  }

  return rand ( $pqRandomStart, $pqRandomEnd );

?>