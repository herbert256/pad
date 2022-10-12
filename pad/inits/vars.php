<?php

  $pad = -1;

  $padTimingsCnt = $padTimings = [];
  $padErrCnt =$padEvalCnt = $padFldCnt = $padCnt = $padOptCnt = $padBtwCnt = $padTypeCnt = $padIgnCnt = 0;
  $padFieldDoubleCheck = $padRestart = '';     

  $padOutput     = '';
  $padStop       = '000';
  $padCacheStop  = 0;
  $padEtag       = '';
  $padExit       = 1;
  $padLen        = 0;
  $padTime       = $_SERVER['REQUEST_TIME'];  

  $padErrrorList = [];

?>