<?php

  $pad           = -1;
  $padRestart    = '';     
  $padOutput     = '';
  $padStop       = '000';
  $padEtag       = '';
  $padExit       = 1;
  $padLen        = 0;
  $padTime       = $_SERVER['REQUEST_TIME'];  
  $padInclude    = FALSE;
  $padCache      = FALSE;
  $padCacheStop  = 0;
  $padBanaan    = [];
  $padPageLevel = [];

  $padLevelVars = [ 'padTag','padType','padPair','padTrue','padFalse','padPrm','padName','padData','padCurrent','padDefault','padWalk','padWalkData','padDone','padOccur','padStart','padEnd','padBase','padHtml','padResult','padHit','padNull','padElse','padArray','padText','padSaveVars','padDeleteVars','padSetSave','padSetDelete','padTagCnt','padAfter','padBefore','padBeforeData','padPrmType','padSet','padGiven','padDeleteSet','padOpt','padOptionsApp','padSaveSet','padTable','padTableTag','padBanaanPAD','padBanaanGlobal','padBanaanSEQ','padBanaan','padSaveLvl','padSaveOcc','padSetLvl','padSetOcc','padBanaanAdd'];

?>