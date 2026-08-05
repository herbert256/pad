<?php

  // randomize, randomize=N - replaces the sequence with N randomly picked entries, or a
  // random ordering of all of them when no count is given. The orderly, duplicates and
  // atLeastOnce tag options are read straight from $padPrm and passed to pqRandom():
  // keep the original order, let an entry be picked more than once, and guarantee every
  // entry is picked at least once. shuffle is the plain reordering.

  $pqRandomOrderly    = $padPrm [$pad] ['orderly']     ?? '';
  $pqRandomDuplicates = $padPrm [$pad] ['duplicates']  ?? '';
  $pqRandomOnce       = $padPrm [$pad] ['atLeastOnce'] ?? '';

  if ( ! $pqActionParm )
    $pqActionCnt = count ( $pqResult );

  $pqResult = pqRandom ( $pqResult, $pqActionCnt, $pqRandomOrderly, $pqRandomDuplicates, $pqRandomOnce );

?>