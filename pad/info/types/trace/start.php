<?php

  include_once PAD . 'info/types/trace/_lib.php';

  set_time_limit ( 900 );

  if ( ! isset ( $padInfoTraceCnt ) ) $padInfoTraceCnt = -1;
  if ( ! isset ( $padInfoTraceId  ) ) $padInfoTraceId  = 0;

  $padInfoTraceCnt++;

  $padInfoTraceLvl [$padInfoTraceCnt] = $pad;

  $padInfoTraceLevel       = [];
  $padInfoTraceLevelChilds = [];
  $padInfoTraceOccurChilds = [];
  $padInfoTraceMaxLevel    = 0;
  $padInfoTraceSkipLevel   = 0;
  $padInfoTraceDir         = "trace/$padPage/$padLog-". $padInfoTraceId;

  if ( $padInfoTraceStartEnd )
    padInfoTrace ( 'trace', 'start', $Result [$pad] ?? '');

?>
