<?php

  $pTimingsCnt = $pTimings = [];
  $pErrCnt =$pEvalCnt = $pFldCnt = $pLvlCnt = $pOptCnt = $pErrCnt = $pTypeCnt = 0;
  $pField_double_check = $pRestart = '';

  $p           = 1;
  $pBetween    = 'start';
  $pOutput     = '';
  $pStop       = '000';
  $pCache_stop = 0;
  $pEtag       = '';
  $pExit       = 1;
  $pLen        = 0;
  $pTime       = $_SERVER['REQUEST_TIME'];  

  $pTrace_dir = "trace/$app-" . str_replace('/', '-', $page) . "/$PADREQID";
  $pLevelDir  = "$pTrace_dir";
  $pOccurDir  = "$pTrace_dir";

  $pErrror_list = [];

?>