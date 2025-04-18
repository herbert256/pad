<?php
 
  if ( is_numeric ($pqLoop) and $pqLoop > PHP_INT_MAX )
    return FALSE;

  $pqTries++;

  if ( $pqTries > $pqTry                              ) return FALSE;
  if ( $pqBuild == 'order' and $pqTries < $pqFrom ) return TRUE;
 
  if ( $pqParmStore ) $pqParm = include 'sequence/build/store.php';
  if ( $pqRandomly  ) $pqLoop = include 'sequence/build/randomly/randomly.php';

  include 'sequence/build/sequence.php';

  if     ( $pq === FALSE ) return TRUE;
  elseif ( $pq === TRUE  ) $pq = $pqLoop;

  if ( count ( $pqPlays ) ) {
    include 'sequence/plays/build.php';
    if ( $pq === FALSE ) 
      return TRUE;
  }

  $pqBase++;

  if ( is_float ($pq)   and $pq < PHP_INT_MIN      ) return FALSE;
  if ( is_float ($pq)   and $pq > PHP_INT_MAX      ) return FALSE; 
  if ( is_numeric ($pq) and $pq < $pqMin       ) return TRUE;
  if ( is_numeric ($pq) and $pq > $pqMax       ) return TRUE;
  if ( $pqUnique and in_array ($pq, $pqResult) ) return TRUE;
  if ( $pqSkip and $pqBase <= $pqSkip )          return TRUE;

  $pqResult [] = $pq;

  if ( is_numeric ($pq) and $pq >= $pqStop     ) return FALSE;
  if ( $pqRows and count($pqResult) >= $pqRows ) return FALSE;

  return TRUE;

?>