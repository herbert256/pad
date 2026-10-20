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

  // Drawn with repeats a count has no ceiling of its own, and randomize=10000000, duplicates
  // ended the request on the memory limit: it is held to the ceiling every run has, and the
  // strict check names a count past it.

  $pqRandomMost = $GLOBALS ['padSeqMaxTries'] ?? 1000000;

  if ( ( $pqRandomDuplicates or $pqRandomOnce ) and $pqActionCnt > $pqRandomMost ) {
    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( "randomize= draws $pqRandomMost values at the most, not $pqActionCnt" );
    $pqActionCnt = $pqRandomMost;
  }

  $pqResult = pqRandom ( $pqResult, $pqActionCnt, $pqRandomOrderly, $pqRandomDuplicates, $pqRandomOnce );

?>
