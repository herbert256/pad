<?php

  // Opens a 'trace' scope: from info/start/config.php for the whole request, or from
  // pad/tags/trace.php for a single {trace} tag - hence the $padInfoTraceCnt stack, each entry
  // remembering in $padInfoTraceLvl the level the scope started at.
  //
  // Clears the per-scope bookkeeping (level paths, child counts, level filters) and fixes
  // $padInfoTraceDir, the DATA/trace/<page>/<log>-<id> directory everything is written under.
  // Tracing is slow enough that the time limit is raised to 900 seconds.

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