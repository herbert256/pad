<?php

  $pqPlaysSet = [];
  $pqInPlays  = TRUE;

  $pqSeqSave   = $pqSeq;
  $pqBuildSave = $pqBuild;
  $pqParmSave  = $pqParm;

  foreach ( $pqPlays as $pqTmp ) {
        
    extract ( $pqTmp );

    $pqLoop = $pq;
    $pqParm = include 'sequence/plays/parm.php';
    $pq     = include "sequence/plays/play/$pqBuild.php";

    if     ( $pqPlay == 'make'   and $pq === TRUE   ) $pq = $pqLoop;

    elseif ( $pqPlay == 'keep'   and $pq === TRUE   ) $pq = $pqLoop;
    elseif ( $pqPlay == 'keep'   and $pq <> $pqLoop ) $pq = FALSE;

    elseif ( $pqPlay == 'remove' and $pq === TRUE   ) $pq = FALSE;
    elseif ( $pqPlay == 'remove' and $pq === FALSE  ) $pq = $pqLoop;
    elseif ( $pqPlay == 'remove' and $pq == $pqLoop ) $pq = FALSE;

    elseif ( $pqPlay == 'flag'   and $pq === TRUE   ) $pq = 1;
    elseif ( $pqPlay == 'flag'   and $pq === FALSE  ) $pq = 0;
    elseif ( $pqPlay == 'flag'   and $pq == $pqLoop ) $pq = 1;
    elseif ( $pqPlay == 'flag'   and $pq <> $pqLoop ) $pq = 0;

    $pqPlaysSet [$pqSeq] = $pq;

    if ( $pq === FALSE )
      break;

  }

  $pqSeq   = $pqSeqSave;
  $pqBuild = $pqBuildSave;
  $pqParm  = $pqParmSave;

  $pqInPlays = FALSE;

?>