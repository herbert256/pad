<?php

  // {pad:ifchanged $customer} ... {/pad:ifchanged}: renders its content when the value differs from
  // what it was the previous time this tag ran in the enclosing loop - a heading per group
  // in a sorted list. The first time it always renders; when the value is the same, the
  // @else@ branch renders instead, if there is one. Several values - {pad:ifchanged $year,
  // $month} - change when any of them does.
  //
  // The memory belongs to one run of the enclosing loop - the nearest level that iterates -
  // so a loop that runs again starts afresh, and to this tag's place in it: the n-th
  // ifchanged an occurrence meets is compared with the n-th of the occurrence before.
  //
  // A level that renders its content once is part of the loop's row, no loop of its own
  // (padLevelInRow): an if, a case, another ifchanged, a {pad:guest}, {pad:auth}, {pad:can} or
  // {pad:feature}, a property pair such as {property:notFirst@items}, a snippet, a {pad:define}d or custom
  // tag drawing the row's heading. Each is a new level on every row, and when only if, case
  // and ifchanged were stepped over the walk stopped at it: every row was a first run and the
  // heading repeated on every row - {items}{pad:guest}{pad:ifchanged $g}...{/pad:guest} gave <x>1 <x>2
  // <y>3 <y>4.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {pad:ifchanged} never closes" );

  if ( trim ( $padOpt [$pad] [0] ?? '' ) === '' and $padCheckSyntax )
    padError ( "the {pad:ifchanged} needs a value - {pad:ifchanged \$customer}" );

  $padIfChangedLoop = $pad - 1;

  while ( $padIfChangedLoop > 0 and padLevelInRow ( $padIfChangedLoop ) )
    $padIfChangedLoop--;

  $padIfChangedRun  = ( $padLevelId [$padIfChangedLoop] ?? 0 ) . '/' . ( $padOccur [$padIfChangedLoop] ?? 0 );
  $padIfChangedLvl  = $padLevelId [$padIfChangedLoop] ?? 0;

  $padIfChangedSeq [$padIfChangedRun] = ( $padIfChangedSeq [$padIfChangedRun] ?? 0 ) + 1;

  $padIfChangedKey  = $padIfChangedLvl . '/' . $padIfChangedSeq [$padIfChangedRun];
  $padIfChangedNow  = serialize ( array_slice ( $padOpt [$pad], 1 ) );

  $padIfChangedSame = ( isset ( $padIfChangedLast [$padIfChangedKey] )
                        and $padIfChangedLast [$padIfChangedKey] === $padIfChangedNow );

  $padIfChangedLast [$padIfChangedKey] = $padIfChangedNow;

  return ! $padIfChangedSame;

?>
