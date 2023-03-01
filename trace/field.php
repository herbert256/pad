<?php

  global $padFldCnt, $padTrace, $pad, $padOccurDir;
 
  $padFldCnt++;

  $padTraceData = [ 
    'tag'       => $prefix,
    'field'     => $field,
    'idx'       => $idx,
    'type'      => $type,
    'result '   => $result
    ];

  padFilePutContents ( $padOccurDir [$pad] . "/fields/$padFldCnt-$field-$type.json", $padTraceData );  

?>