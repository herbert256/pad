<?php
 
  $pqTries++;

  if ( $pqTries > $pqTry ) 
    return FALSE;
 
  if ( $pqParmStore ) $pqParm = include 'sequence/build/store.php';
  if ( $pqRandomly  ) $pqLoop = include 'sequence/build/randomly/randomly.php';

      if ( pqStore ( $pqBuild ) )  $pq = $pqLoop;
  elseif ($pqBuild == 'bool'    )  $pq = ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );
  elseif ($pqBuild == 'function')  $pq = ( 'pq'     . ucfirst($pqSeq) ) ( $pqLoop );
  elseif ($pqBuild == 'check'   )  $pq = include "sequence/build/check.php";
  elseif ($pqBuild == 'loop'    )  $pq = include "sequence/types/$pqSeq/loop.php";
  elseif ($pqBuild == 'make'    )  $pq = include "sequence/types/$pqSeq/make.php";
  elseif ($pqBuild == 'order'   )  $pq = include "sequence/types/$pqSeq/order.php";

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