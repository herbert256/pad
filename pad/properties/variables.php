<?php

  // The variables@tag property: every level variable of level $padIdx as an iterable data
  // set.
  //
  // padDataForcePad() turns the $padSetLvl map into name/value rows so a template can walk
  // it; variable.php picks out a single one. Occurrence variables (%var, $padSetOcc) are
  // not included.

  global $padSetLvl;

  $padTagParmsResult = $padSetLvl [$padIdx];
  $padTagParmsResult = padDataForcePad ($padTagParmsResult);

  return $padTagParmsResult;

?>