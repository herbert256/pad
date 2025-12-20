<?php

  if ( str_contains ( $pqParm, '%' ) ) {

    $pqChance = str_replace('%', '', $pqParm );

    if  ( mt_rand ( 1, 100 ) <= $pqChance  ) return TRUE;
    else                                     return FALSE;

  } else {

    if  ( mt_rand ( 1, $pqParm ) == 1 ) return TRUE;
    else                                return FALSE;

  }

?>
