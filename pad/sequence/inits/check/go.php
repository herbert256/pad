<?php

  // Applies the one play mode named in $pqCheck, called four times over by
  // inits/check/check.php.
  //
  // Returns straight away when that mode was not asked for, or when a play already carries a
  // mode - plays/inits.php may have set one from the option value. Otherwise the build
  // strategy is recomputed for the mode: a run whose own build is a store (pull, fixed, build
  // or given) has no generation step to flag, so inits/check/store.php marks its play
  // instead; a generated run has its own $pqBuild re-derived by inits/check/sequence.php.

  if ( $pqCheck == 'make'   and ! $pqMake   ) return;
  if ( $pqCheck == 'flag'   and ! $pqFlag   ) return;
  if ( $pqCheck == 'keep'   and ! $pqKeep   ) return;
  if ( $pqCheck == 'remove' and ! $pqRemove ) return;

  foreach ( $pqPlays as $pqPlay )
    if ( $pqPlay ['pqPlay'] == $pqCheck )
      return;

  if ( pqStore ( $pqBuild ) ) include PQ . 'inits/check/store.php';
  else                        include PQ . 'inits/check/sequence.php';

?>