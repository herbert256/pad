<?php

  // Opens a run started from an expression rather than from a template tag: the parameters
  // come from $pqSetParms, not from the level.
  //
  // The expression evaluator runs inside a function, so every engine global the rest of the
  // subsystem reads - the level state, the sequence store and the row/try defaults from
  // config/sequence.php - has to be pulled into scope here.

  $pqEntry = 'direct';

  global $pqStore;
  global $pad, $padPrm, $padType, $padTag, $padPrefix, $padLastPush;
  global $padData, $padParms, $padInfo, $padDataStore, $padSeqDefaultRows, $padSeqDefaultTries;

?>