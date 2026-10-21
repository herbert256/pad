<?php

  // {cease 'tag'} ends a loop gracefully, where {break} ends it abruptly: no jump is
  // made, so the current pass and the rest of the body still render, but the loop has
  // nothing left to iterate afterwards.
  //
  // padFindContinueBreak() picks the level - by name, by number, by negative offset, or
  // the nearest enclosing loop - and every element of its data after that level's current
  // key is dropped. It has to be the loop's own $padKey: this used to read the cease tag's,
  // which level/setup.php had just initialised to 1, so the truncation point was a constant
  // - right whenever the loop happened to stand on key 1, wrong on any other row, and never
  // reached at all over string keys.
  //
  // The keys are compared as they are: both come from the same array, so they have its own
  // key types, an int for "7" and a string for "007". Compared with ==, PHP took 7, "007" and
  // "7.0" for one number: the walk marked the first such key as the current one and never
  // dropped a later one that matched as well - a cease on the first row kept the second and
  // the fourth.

  $padCeaseLevel = padFindContinueBreak ( $padParm );

  $padCease = FALSE;

  foreach ( $padData [$padCeaseLevel] as $padK => $padV )

    if ( $padK === $padKey [$padCeaseLevel] )

      $padCease = TRUE;

    elseif ( $padCease )

      unset (  $padData [$padCeaseLevel] [$padK] );

  // A {while} or {until} iterates by re-running its handler while $padWalk is 'next', not
  // over a data set, so dropping the rows behind the current one left it looping and
  // {cease} did nothing at all - {while $i lt 6}...{cease}...{/while} ran to 6. Clearing
  // the walk lets the current pass finish, as cease promises, and asks for no further one.
  // Only the 'next' walk is cleared: a two-pass tag ({markdown}, toData and the rest) carries
  // 'end' for the pass that post-processes its result, which clearing dropped.

  if ( $padWalk [$padCeaseLevel] == 'next' )
    $padWalk [$padCeaseLevel] = '';

  return TRUE;

?>
