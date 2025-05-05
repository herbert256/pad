<?php
 
  $pqTries++;

  if ( $pqTries > $pqTry ) 
    return FALSE;
 
  if ( $pqParmStore ) $pqParm = include 'sequence/build/store.php';
  if ( $pqRandomly  ) $pqLoop = include 'sequence/build/randomly/randomly.php';

  $pq = ( pqStore ( $pqBuild ) ) ? $pqLoop : include "sequence/build/main/$pqBuild.php";

  if     ( $pq === FALSE ) return TRUE;
  elseif ( $pq === TRUE  ) $pq = $pqLoop;

  $pqOrgSet = $pq;

  if ( count ( $pqPlays ) ) {
    $pqPlaysSet = [];
    include 'sequence/plays/plays.php';
    if ( $pq === FALSE ) 
      return TRUE;
  }

  if ( is_float ($pq)   and $pq < PHP_INT_MIN  ) return FALSE;
  if ( is_float ($pq)   and $pq > PHP_INT_MAX  ) return FALSE; 
  if ( is_numeric ($pq) and $pq < $pqMin       ) return TRUE;
  if ( is_numeric ($pq) and $pq > $pqMax       ) return TRUE;
  if ( $pqUnique and in_array ($pq, $pqResult) ) return TRUE;
  if ( $pqSkip and $pqTries <= $pqSkip )         return TRUE;

  if ( $pqBuild == 'order' ) { 
    $pqOrder [] = $pq;
    if ( $pqLoop < $pqOrderFrom ) 
      return TRUE;
  }
  
  $pqResult [] = $pq;
  $pqOrgHit [] = $pqOrgSet;

  if ( count ( $pqPlays ) ) 
    $pqPlaysHit [] = $pqPlaysSet;

  if ( is_numeric ($pq) and $pq >= $pqStop     ) return FALSE;
  if ( $pqRows and count($pqResult) >= $pqRows ) return FALSE;

  return TRUE;

?>