<?php

  // The before= form of a callback: the entire data set is passed through the callback up
  // front - init, then every row, then exit - before the level renders anything.
  //
  // Rows are handed to padCallbackBeforeRow by reference, so the callback can rewrite or
  // extend $padData [$pad] before it is walked. The alternative is the streaming form,
  // where the row phase runs per occurrence instead.

  padCallbackBeforeXxx ('init');

  foreach ( $padData [$pad] as $padK => $padV)
    padCallbackBeforeRow ( $padData [$pad] [$padK] );

  padCallbackBeforeXxx ('exit');

?>