<?php

  $pad = -1;

  $padCache = FALSE;

  $padBase = $padHtml = $padResult = $padTimingsCnt = $padTimings = [];
  $padErrCnt = $padLogCnt = $padTrcCnt =$padEvalCnt = $padFldCnt = $padCnt = $padOptCnt = $padIgnCnt = 0;
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

  $padErrrorList = [];

?>