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

?>
