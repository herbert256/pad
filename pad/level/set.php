<?php

  // Publishes this level's $name= assignments as PHP globals, so tag handlers and app code
  // can read them. padSetGlobalLvl() remembers what it overwrote, in $padSaveLvl and
  // $padDeleteLvl, so padResetLvl() can put it back when the level closes.

  // Which of the names are globals already is found for the level at once (lib/scope.php):
  // the few names looked up in the symbol table, not its some 450 entries in the names.

  $padSetHave = $padSetLvl [$pad] ? array_intersect_key ( $padSetLvl [$pad], $GLOBALS ) : [];

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    padSetGlobalLvl ( $padK, $padV, array_key_exists ( $padK, $padSetHave ) );

  unset ( $padSetHave );

  // From here on the level's variables are globals, and a lookup reads them there - where a
  // {set} inside the level may have changed them. Until now they were only the values the
  // parameters registered, which lib/field/level.php answers itself (padFieldLevel).

  $padSetDone [$pad] = TRUE;

?>
