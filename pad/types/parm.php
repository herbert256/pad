<?php

  if ( isset($padPrmsVal [$pad][0]) )
    $padFieldTag = $padPrmsVal [$pad][0];
  else
    $padFieldTag = '';

  $padFieldResult = padFieldTag ("$padFieldTag#$padTag[$pad]");

  if ( $padFieldResult === INF )
    return NULL;
  else
    return $padFieldResult;

?>