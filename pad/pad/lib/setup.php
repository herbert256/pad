<?php

  $padEntryType = 'entry-' . $padEntryType;

  $padBetween   = $padEntryType;
  $padWords     = [];
  $padWords [0] = $padEntryType;

  $padTypeCheck  = $padEntryType;
  $padTypeResult = 'internal';
  $padTypeGiven  = FALSE;
  $padPairSet    = FALSE;
  $padTrueSet    = '';
  $padPrmTypeSet = 'none';

  include pad . 'level/setup.php';

?>