<?php

  // Fires from pad/eval/fast.php, the shortcut that runs a bare built-in pipe function
  // straight from PAD . functions/<name>.php without going through expression parsing.
  //
  // Xref only: files the function under functions/parms as kind 'pad', so fast-path uses
  // land in the same place as the ones events/functionParms.php reports.

  global $padInfoXref;

  if ( $padInfoXref  )
    padInfoXref ( 'functions/parms', 'pad', $eval );

?>