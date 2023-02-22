<?php

  $padParseCount++;

  $padParseLevel [$pad] = $padParseCount;
  $padParseFalse [$pad] = $padFalse [$pad];
  $padParseInfo  [$pad] = 'level';

  $padParseResult [$padParseCount] = [
    'type'   => 'LevelStart', 
    'parse'  => $padParseLevel [$pad], 
    'lvl'    => $pad,
    'tag'    => $padTag     [$pad],
    't-type' => $padType    [$pad],
    'pair'   => $padPair    [$pad],
    'p-type' => $padPrmType [$pad],
    'prm'    => $padPrm     [$pad],
    'true'   => $padTrue    [$pad],
    'false'  => $padFalse   [$pad]
  ];

  include PAD . 'occurrence/start.php'; 

?>