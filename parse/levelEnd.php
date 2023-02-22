<?php

  $padParseCount++;

  $padParseResult [$padParseCount] = [
    'type'   => 'LevelEnd',
    'parse'  => $padParseLevel [$pad], 
    'info '  => $padParseInfo  [$pad], 
    'result' => $padResult [$pad]
  ]; 

  if ( $padParseFalse [$pad] ) {
  
    $padParseInfo [$pad] = 'false';
    padRetrieveContent ( $padParseFalse [$pad] );
  
    $padParseCount++;
  
  }

?>