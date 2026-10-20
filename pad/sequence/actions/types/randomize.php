<?php

  // randomize, randomize=N - replaces the sequence with N randomly picked entries, or a
  // random ordering of all of them when no count is given. The orderly, duplicates and
  // atLeastOnce tag options are read straight from $padPrm and passed to pqRandom():
  // keep the original order, let an entry be picked more than once, and guarantee every
  // entry is picked at least once. shuffle is the plain reordering.

  // Options of the tag's own run only: an action from an expression - sequence:randomize([..])
  // in an eval= - drew with the duplicates or orderly of the tag the expression stands in.

  $pqRandomTag        = ( $pqEntry == 'tag' ) ? $padPrm [$pad] : [];

  $pqRandomOrderly    = $pqRandomTag ['orderly']     ?? '';
  $pqRandomDuplicates = $pqRandomTag ['duplicates']  ?? '';
  $pqRandomOnce       = $pqRandomTag ['atLeastOnce'] ?? '';

  // Bare, it draws them all; a count of 0 draws none - see pqRandom().

  if ( (string) $pqActionParm === '' )
    $pqActionCnt = count ( $pqResult );

  $pqResult = pqRandom ( $pqResult, $pqActionCnt, $pqRandomOrderly, $pqRandomDuplicates, $pqRandomOnce );

?>
