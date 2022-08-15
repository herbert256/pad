<?php

  $padStore_name = $padPrmsTag [$pad] ['toData'];

  if ( !$padPair and !$padContent and !pIs_default_data($padData [$pad]) ) {
    $padDataStore [$padStore_name] = $padData [$pad];
    return;
  }

  if ( $padWalk  [$pad] <> 'start' ) 
    $padDataStore [$padStore_name] = $padWalkData  [$pad];
  else
    $padDataStore [$padStore_name] = $padData [$pad];

  $padResult [$pad] = '';
  
?>