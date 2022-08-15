<?php

  if ( isset($padPrmsVal [$pad][0]) )
    $padField_tag = $padPrmsVal [$pad][0];
  else
    $padField_tag = '';

  $padFieldResult = padFieldTag ("$padField_tag#$padTag[$pad]");

  if ( $padFieldResult === INF )
    return NULL;
  else
    return $padFieldResult;

?>