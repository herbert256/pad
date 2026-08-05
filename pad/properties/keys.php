<?php

  // The keys@tag property: the data set of level $padIdx turned into an iterable list of
  // name/value pairs, one per key.
  //
  // The name is the row's key; the value is the row itself, run through padDataForcePad()
  // so that it too can be walked as name/value pairs.

  global $padData;

  $padReturn = [];

  foreach ( $padData [$padIdx] as $padK => $padV) {
    $padReturn [$padK] ['name'] = $padK;
    $padReturn [$padK] ['value'] = padDataForcePad ($padV);
  }

  return $padReturn;

?>