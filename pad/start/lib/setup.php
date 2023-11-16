<?php

  $padStartType = 'entry-' . $padStartType;
  $padStartType = 'PAD';

  $padBetween   = $padStartType;
  $padWords     = [];
  $padWords [0] = $padStartType;

  $padTypeCheck  = $padStartType;
  $padTypeResult = 'internal';
  $padTypeGiven  = FALSE;
  $padPairSet    = FALSE;
  $padTrueSet    = '';
  $padPrmTypeSet = 'none';

  include pad . 'level/setup.php';

?>