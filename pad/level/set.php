<?php

  // Publishes this level's $name= assignments as PHP globals, so tag handlers and app code
  // can read them. padSetGlobalLvl() remembers what it overwrote, in $padSaveLvl and
  // $padDeleteLvl, so padResetLvl() can put it back when the level closes.

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    padSetGlobalLvl ( $padK, $padV );

?>