<?php

  $pTimings_count = $pTimings = [];
  $pErr_cnt =$pEval_cnt = $pFld_cnt = $pLvl_cnt = $pOpt_cnt = $pErr_cnt = $pType_cnt = 0;
  $pField_double_check = $pRestart = '';

  $pad            = 1;
  $pBetween    = 'start';
  $pOutput     = '';
  $pStop       = '000';
  $pCache_stop = 0;
  $pEtag       = '';
  $pExit       = 1;
  $pLen        = 0;
  $pTime       = $_SERVER['REQUEST_TIME'];  

  $pTrace_dir = "trace/$app-" . str_replace('/', '-', $page) . "/$PADREQID";
  $pLevel_dir  = "$pTrace_dir";
  $pOccur_dir  = "$pTrace_dir";

  $pErrror_list = [];

?>