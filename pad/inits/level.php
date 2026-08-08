<?php

  // Opens the root level, the one every tag in the page nests inside.
  //
  // Rather than special-casing it, this fakes a tag named 'internal' and runs the ordinary
  // machinery: level/between.php parses that name into $padTagCheck / $padTagOpts, and
  // level/setup.php then steps $pad from -1 to 0 and creates the level 0 entry in each of
  // the arrays listed in padLevelVars. The $padTypeCheck..$padTagResult assignments in
  // between are the seed values level/setup.php copies into $padTag, $padType, $padPair,
  // $padBase and friends for that root level.
  //
  // build/build.php then fills $padBase[0] with the assembled page, and start/pad/level.php
  // runs the loop over it.

  $padBetween = 'internal';
  include PAD . 'level/between.php';

  // $padTypeSeq is seeded here for the same reason as the others, and for one more: the
  // nested pass of start/pad/function.php can only alias globals that exist when it opens
  // its scope. A global born later - as this one was, inside padTypeSeq() - splits into a
  // scope copy and a global, and level/tag.php then builds a prefixed sequence pair name
  // from the blank copy: {prime:keep}...{/prime:keep} inside padCode()/padSandbox() lost
  // its closing tag that way in a fresh request.

  $padTypeCheck  = 'internal';
  $padTypeResult = '';
  $padTypeSeq    = '';
  $padTypeGiven  = FALSE;
  $padPairSet    = FALSE;
  $padBaseSet    = '';
  $padPrmTypeSet = 'none';
  $padTagResult  = 'internal';

  include PAD . 'level/setup.php';

?>