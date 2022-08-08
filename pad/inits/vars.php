<?php

  $pTimingsCnt = $pTimings = [];
  $pErrCnt =$pEvalCnt = $pFldCnt = $pCnt = $pOptCnt = $pErrCnt = $pBtwCnt = $pTypeCnt = $pIgnCnt = 0;
  $pField_double_check = $pRestart = '';     

  $pOutput     = '';
  $pStop       = '000';
  $pCache_stop = 0;
  $pEtag       = '';
  $pExit       = 1;
  $pLen        = 0;
  $pTime       = $_SERVER['REQUEST_TIME'];  

  $pErrror_list = [];

?>