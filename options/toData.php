<?php

  $padStoreName = $padPrm [$pad] ['toData'];

  if ( !$padPair and !$padContent and !padIsDefaultData($padData [$pad]) ) {
    $padDataStore [$padStoreName] = $padData [$pad];
    return;
  }

  if ( $padWalk  [$pad] <> 'start' ) 
    $padDataStore [$padStoreName] = $padWalkData [$pad];
  else
    $padDataStore [$padStoreName] = $padData [$pad];

  $padResult [$pad] = '';
  
?>