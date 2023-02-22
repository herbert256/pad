<?php

  $padParseCount++;
  $padParseLevel [$pad] = $padParseCount;
  $padParseInfo  [$pad] = 'start';

  $padParseResult [$padParseCount] = [
    'type'   => 'Start', 
    'base'   =>  $padBase [$pad]  
  ];

  padRetrieveContent ( $padBase [$pad] );

  include 'stop.php';

?>