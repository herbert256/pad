<?php

  // The options@tag property: every named option of level $padIdx - {items sort="name"
  // reverse} - as an iterable data set.
  //
  // padDataForcePad() turns the $padPrm map into name/value rows so a template can walk
  // it; option.php picks out a single one.

  global $padPrm;

  $padTagParmsResult = $padPrm [$padIdx];
  $padTagParmsResult = padDataForcePad ($padTagParmsResult);

  return $padTagParmsResult;

?>