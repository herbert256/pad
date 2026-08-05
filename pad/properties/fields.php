<?php

  // The fields@tag property: the fields of the row being rendered, as an iterable data set
  // of name/value pairs numbered from 1.
  //
  // Lets a template walk a record whose columns it does not know in advance:
  // {fields@user}{$name}: {$value}{/fields}.

  global $padCurrent;

  $padReturn = [];
  $padI      = 0;

  foreach ($padCurrent [$padIdx] as $padK => $padV) {
    $padI++;
    $padReturn [$padI] ['name']  = $padK;
    $padReturn [$padI] ['value'] = $padV;
  }

  return $padReturn;

?>