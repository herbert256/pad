<?php

  // The {set $name = value} tag: makes the level's variable assignments stick as globals.
  //
  // Parsing has already evaluated every $name=value pair into $padSetLvl [$pad]; this copies
  // them into $GLOBALS and then empties $padSaveLvl/$padDeleteLvl, which is exactly what
  // stops padResetLvl() from rolling them back when the level closes. Refused as an
  // open/close pair, since a {set} has no content to scope the assignment to.

  if ( $padPair [$pad] )
    return padError ("{set ...} can not be used as a open/close tag");

  foreach ( $padSetLvl [$pad] as $padSetName => $padSetValue )
    $GLOBALS [$padSetName] = $padSetValue;

  $padSaveLvl [$pad] = $padDeleteLvl [$pad] = [];

  return TRUE;

?>