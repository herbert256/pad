<?php

  // Runs the registered plays over one candidate term - the filter and transform stage.
  //
  // Included by build/one.php with the candidate in $pq, whenever the tag carried any
  // {make}/{keep}/{remove}/{flag} option. Each $pqPlays entry names a sequence, a parm and
  // its own build strategy; a play is evaluated by including plays/play/$pqBuild.php,
  // which returns TRUE, FALSE, or the term that sequence has at this position.
  //
  // That raw answer is then reinterpreted according to the kind of play: make substitutes
  // the returned value, keep drops the term unless it matches, remove drops it when it
  // does, flag replaces it with 1 or 0. A FALSE outcome rejects the term and ends the
  // chain. Every play's own answer is kept in $pqPlaysSet, which build/one.php stores so
  // it can be published as an extra field per row. $pqSeq/$pqBuild/$pqParm belong to the
  // main sequence and are saved and restored around the loop.

  $pqPlaysSet  = [];
  $pqSeqSave   = $pqSeq;
  $pqBuildSave = $pqBuild;
  $pqParmSave  = $pqParm;

  foreach ( $pqPlays as $pqTmp ) {

    extract ( $pqTmp );

    $pqLoop = $pq;
    $pqParm = include PQ . 'plays/parm.php';
    $pq     = include PQ . "plays/play/$pqBuild.php";

    if     ( $pqPlay == 'make'   and $pq === TRUE   ) $pq = $pqLoop;

    elseif ( $pqPlay == 'keep'   and $pq === TRUE   ) $pq = $pqLoop;
    elseif ( $pqPlay == 'keep'   and $pq != $pqLoop ) $pq = FALSE;

    elseif ( $pqPlay == 'remove' and $pq === TRUE   ) $pq = FALSE;
    elseif ( $pqPlay == 'remove' and $pq === FALSE  ) $pq = $pqLoop;
    elseif ( $pqPlay == 'remove' and $pq == $pqLoop ) $pq = FALSE;

    elseif ( $pqPlay == 'flag'   and $pq === TRUE   ) $pq = 1;
    elseif ( $pqPlay == 'flag'   and $pq === FALSE  ) $pq = 0;
    elseif ( $pqPlay == 'flag'   and $pq == $pqLoop ) $pq = 1;
    elseif ( $pqPlay == 'flag'   and $pq != $pqLoop ) $pq = 0;

    $pqPlaysSet [$pqSeq] = $pq;

    if ( $pq === FALSE )
      break;

  }

  $pqSeq   = $pqSeqSave;
  $pqBuild = $pqBuildSave;
  $pqParm  = $pqParmSave;

?>