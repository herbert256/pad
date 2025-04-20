<?php 

  if ( $pqParm and isset ( $pqStore [$pqParm] ) )
    include 'sequence/plays/store.php';

  if ( is_numeric ($pqParm) )
    if ( str_contains ( $pqParm, '.' ) ) $pqParm = doubleval ( $pqParm );
    else                                     $pqParm = intval    ( $pqParm );

  include 'sequence/build/include.php';

  $pq = include 'sequence/build/call.php';

  if     ( $pqPlay == 'loop'   and $pq === TRUE   ) $pq = $pqLoop;

  elseif ( $pqPlay == 'make'   and $pq === TRUE   ) $pq = $pqLoop;

  elseif ( $pqPlay == 'keep'   and $pq === TRUE   ) $pq = $pqLoop;
  elseif ( $pqPlay == 'keep'   and $pq <> $pqLoop ) $pq = FALSE;

  elseif ( $pqPlay == 'remove' and $pq === TRUE   ) $pq = FALSE;
  elseif ( $pqPlay == 'remove' and $pq === FALSE  ) $pq = $pqLoop;
  elseif ( $pqPlay == 'remove' and $pq == $pqLoop ) $pq = FALSE;

  elseif ( $pqPlay == 'flag'   and $pq === TRUE   ) $pq = 1;
  elseif ( $pqPlay == 'flag'   and $pq === FALSE  ) $pq = 0;
  elseif ( $pqPlay == 'flag'   and $pq == $pqLoop ) $pq = 1;
  elseif ( $pqPlay == 'flag'   and $pq <> $pqLoop ) $pq = 0;

  if ( ! isset ( $pqInfo ['plays'] ) or ! in_array ( "$pqPlay/$pqSeq", $pqInfo ['plays'] ) )
    $pqInfo ['plays'] [] = "$pqPlay/$pqSeq";

?>