<?php

  $padParseCount++;

  $padParseResult [$padParseCount] = [
    'tag'    => $padTag     [$pad],
    't-type' => $padType    [$pad],
    'level'  => $pad,
    'true'   => str_replace("\n", '', $padTrue    [$pad]),
    'false'  => str_replace("\n", '', $padFalse   [$pad])
  ];

  $padParseLevel [$pad] = $padParseCount;
  $padParseInfo  [$pad] = 'true';

  $padParseFalse [$pad] = $padFalse [$pad];

  if ( $padPrmType [$pad] <> 'none' ) {
    $padParseResult [$padParseCount] ['p-type'] = $padPrmType [$pad];
    $padParseResult [$padParseCount] ['prm']    = $padPrm     [$pad];
  }
    
  include PAD . 'occurrence/start.php'; 

?>