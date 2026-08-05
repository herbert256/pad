<?php

  // The {restart} tag: abandons the page being built and has the engine run $padParm instead,
  // passing on every variable {set} at this level ($padSetLvl [$pad]) as a global.
  //
  // padRestart() only records the wish in $padRestart and returns NULL, so the tag reads as
  // null here and the actual switch happens in start/restart.php when level/level.php notices.

  return padRestart ( $padParm, $padSetLvl [$pad] );

?>