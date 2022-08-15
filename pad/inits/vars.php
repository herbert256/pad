<?php

  $pad = -1;

  $padTimingsCnt = $padTimings = [];
  $padErrCnt =$padEvalCnt = $padFldCnt = $padCnt = $padOptCnt = $padErrCnt = $padBtwCnt = $padTypeCnt = $padIgnCnt = 0;
  $padField_double_check = $padRestart = '';     

  $padOutput     = '';
  $padStop       = '000';
  $padCache_stop = 0;
  $padEtag       = '';
  $padExit       = 1;
  $padLen        = 0;
  $padTime       = $_SERVER['REQUEST_TIME'];  

  $padErrror_list = [];

?>