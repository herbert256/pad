<?php

  $pad = -1;

  $padCache = FALSE;

  $padParseCount = 0;
  
  $padBase = $padHtml = $padResult = $padTimingsCnt = $padTimings = [];
  $padSqlCnt = $padErrCnt = $padTrcCnt =$padEvalCnt = $padFldCnt = $padCnt = $padOptCnt = $padIgnCnt = 0;
  $padFieldDoubleCheck = $padRestart = '';     

  $padOutput     = '';
  $padStop       = '000';
  $padCacheStop  = 0;
  $padEtag       = '';
  $padExit       = 1;
  $padLen        = 0;
  $padTime       = $_SERVER['REQUEST_TIME'];  
  $padError      = '';
  $padDump       = '';
  $padInOccur    = FALSE;
  $padInclude    = FALSE;

  $padErrrorList = [];

?>