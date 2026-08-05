<?php

  // {cease 'tag'} ends a loop gracefully, where {break} ends it abruptly: no jump is
  // made, so the current pass and the rest of the body still render, but the loop has
  // nothing left to iterate afterwards.
  //
  // padFindContinueBreak() picks the level - by name, by number, by negative offset, or
  // the nearest enclosing loop - and every element of its data after the key $padKey
  // [$pad] is dropped.

  $padCeaseLevel = padFindContinueBreak ( $padParm );

  $padCease = FALSE;

  foreach ( $padData [$padCeaseLevel] as $padK => $padV )

    if ( $padK == $padKey [$pad] )

      $padCease = TRUE;

    elseif ( $padCease )

      unset (  $padData [$padCeaseLevel] [$padK] );

  return TRUE;

?>