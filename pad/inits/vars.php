<?php

  $pad           = -1;
  $padRestart    = '';     
  $padOutput     = '';
  $padStop       = '000';
  $padEtag       = '';
  $padExit       = 1;
  $padLen        = 0;
  $padTime       = $_SERVER['REQUEST_TIME'];  
  $padCache      = FALSE;
  $padCacheStop  = 0;
  $padPageLevel  = [];

  $padFlagStore = $padSeqStore = $padDataStore = $padSeqStore = [];

  $padLevelVars = [ 'padTag','padType','padPair','padTrue','padFalse','padPrm','padName','padData','padCurrent','padDefault','padWalk','padWalkData','padDone','padOccur','padStart','padEnd','padBase','padHtml','padResult','padHit','padNull','padElse','padArray','padText','padSaveVars','padDeleteVars','padSetSave','padSetDelete','padTagCnt','padAfter','padBefore','padBeforeData','padPrmType','padSet','padGiven','padDeleteSet','padOpt','padOptionsApp','padSaveSet','padTable','padTableTag','padSaveLvl','padSaveOcc','padSetLvl','padSetOcc','padDeleteOcc','padDeleteLvl','padcontent','padPagePad','padPageApp','padCount','padKey'];

?>