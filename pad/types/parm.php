<?php

  if ( isset($padPrm [$pad][1]) )
    $padFieldTag = $padPrm [$pad][1];
  else
    $padFieldTag = '';

  $padFieldResult = padFieldTag ("$padFieldTag#$padTag[$pad]");

  if ( $padFieldResult === INF )
    return NULL;
  else
    return $padFieldResult;

?>