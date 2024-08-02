<?php

  $GLOBALS ['padLevelVars'] = [ 'padTag','padType','padPair','padPrm','padName','padData','padCurrent','padDefault','padWalk','padWalkData','padDone','padOccur','padStart','padEnd','padBase','padPad','padResult','padHit','padNull','padElse','padArray','padSaveVars','padDeleteVars','padSetSave','padSetDelete','padTagCnt','padAfter','padBefore','padBeforeData','padPrmType','padSet','padGiven','padDeleteSet','padOpt','padOptionsAppStart','padSaveSet','padTable','padTableTag','padSaveLvl','padSaveOcc','padSetLvl','padSetOcc','padDeleteOcc','padDeleteLvl','padPagePad','padPageApp','padCount','padKey','padTraceIds','padTraceOccurId','padTraceLevelChilds','padTraceOccurChilds','padTraceOccur','padTraceLevel','padTraceOccurTag','padTraceOccOpen','padTraceOccClose','padXmlLevel','padAfterBase','padBeforeBase','padDouble','padEndBase','padOccurStart','padOccurType','padStartBase','padStartData','padXrefLevel','padLvlIds','padParmParse'
  ];
  
  $padBetween = '*internal*';
  include pad . 'level/between.php';

  $padTypeCheck  = '*internal*';
  $padTypeResult = '';
  $padTypeGiven  = FALSE;
  $padPairSet    = FALSE;
  $padBaseSet    = '';
  $padPrmTypeSet = 'none';

  include pad . 'level/setup.php';

?>