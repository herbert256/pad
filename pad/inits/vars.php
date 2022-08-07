<?php

  $pTimingsCnt = $pTimings = [];
  $pErrCnt =$pEvalCnt = $pFldCnt = $pCnt = $pOptCnt = $pErrCnt = $pTypeCnt = 0;
  $pField_double_check = $pRestart = '';

  $p           = 0;
  $pBetween    = 'start';
  include PAD . 'level/between.php';      
  include PAD . 'level/setup.php';      

  $pOutput     = '';
  $pStop       = '000';
  $pCache_stop = 0;
  $pEtag       = '';
  $pExit       = 1;
  $pLen        = 0;
  $pTime       = $_SERVER['REQUEST_TIME'];  

  $pErrror_list = [];

?>