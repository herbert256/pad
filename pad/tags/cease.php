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

  $padCeaseLevel = padFindContinueBreak ( $padParm );

  $padCease = FALSE;

  foreach ( $padData [$padCeaseLevel] as $padK => $padV )

    if ( $padK == $padKey [$padCeaseLevel] )

      $padCease = TRUE;

    elseif ( $padCease )

      unset (  $padData [$padCeaseLevel] [$padK] );

  return TRUE;

?>