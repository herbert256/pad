<?php

  // The {set $name = value} tag: makes the level's variable assignments stick as globals.
  //
  // Parsing has already evaluated every $name=value pair into $padSetLvl [$pad]; this copies
  // them into $GLOBALS and then empties $padSaveLvl/$padDeleteLvl, which is exactly what
  // stops padResetLvl() from rolling them back when the level closes. Refused as an
  // open/close pair, since a {set} has no content to scope the assignment to.

  // Refused under the strict syntax check; the lenient walk lets the assignments stick
  // and renders the content like any other pair.

  if ( $padPair [$pad] and $padCheckSyntax )
    padError ("{set ...} can not be used as a open/close tag");

  // A {set} whose words parsed into no assignment - a missing $, a missing value - used
  // to do nothing at all. Strict mode says so.

  if ( $padCheckSyntax and ! count ( $padSetLvl [$pad] ) )
    padError ( "the {set} assigns nothing - it speaks in \$name = value pairs" );

  foreach ( $padSetLvl [$pad] as $padSetName => $padSetValue )
    $GLOBALS [$padSetName] = $padSetValue;

  $padSaveLvl [$pad] = $padDeleteLvl [$pad] = [];

  return TRUE;

?>