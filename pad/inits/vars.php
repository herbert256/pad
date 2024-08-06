<?php

  $pad          = -1;
  $padLvlId     = 0;
  $padRestart   = '';     
  $padOutput    = '';
  $padStop      = '000';
  $padEtag      = '';
  $padLen       = 0;
  $padTime      = $_SERVER ['REQUEST_TIME'];  
  $padCacheStop = 0;
  $padPageLevel = [];
  $padBuffer    = '';
  $padInclude   = isset ( $_REQUEST ['padInclude'] ) ? TRUE : FALSE;
  $padStrCnt    = -1;
  $padStrFunCnt = 0;

  $padLevelVars = [ 'padTag','padType','padPair','padPrm','padName','padData','padCurrent','padDefault','padWalk','padWalkData','padDone','padOccur','padStart','padEnd','padBase','padPad','padResult','padHit','padNull','padElse','padArray','padSaveVars','padDeleteVars','padSetSave','padSetDelete','padTagCnt','padAfter','padBefore','padBeforeData','padPrmType','padSet','padGiven','padDeleteSet','padOpt','padOptionsAppStart','padSaveSet','padTable','padTableTag','padSaveLvl','padSaveOcc','padSetLvl','padSetOcc','padDeleteOcc','padDeleteLvl','padPagePad','padPageApp','padCount','padKey','padTraceIds','padTraceOccurId','padTraceLevelChilds','padTraceOccurChilds','padTraceOccur','padTraceLevel','padTraceOccurTag','padTraceOccOpen','padTraceOccClose','padXmlLevel','padAfterBase','padBeforeBase','padDouble','padEndBase','padOccurStart','padOccurType','padStartBase','padStartData','padXrefLevel','padLvlIds','padParmParse','padLvlFunVar','padLvlFun'
  ];

  $padStrSto = ['padDataStore','padContentStore','padFlagStore','padSeqStore'];
  $padStrDat = ['padData','padCurrent','padSetLvl','padSetOcc','padTable','padPrm','padOpt'];

?>