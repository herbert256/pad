<?php

  define ( 'padLevelVars', [ 'padTag','padType','padPair','padPrm','padName','padData','padCurrent','padDefault','padWalk','padWalkData','padDone','padOccur','padStart','padEnd','padBase','padOut','padResult','padHit','padNull','padElse','padArray','padSaveVars','padDeleteVars','padSetSave','padSetDelete','padTagCnt','padAfter','padBefore','padBeforeData','padPrmType','padSet','padGiven','padDeleteSet','padOpt','padOptionsAppStart','padSaveSet','padSaveLvl','padSaveOcc','padSetLvl','padSetOcc','padDeleteOcc','padDeleteLvl','padPagePad','padPageApp','padCount','padKey','padInfoTraceIds','padInfoTraceOccurId','padInfoTraceLevelChilds','padInfoTraceOccurChilds','padInfoTraceOccur','padInfoTraceLevel','padInfoTraceOccurTag','padInfoTraceOccOpen','padInfoTraceOccClose','padXmlLevel','padAfterBase','padBeforeBase','padDouble','padEndBase','padOccurStart','padOccurType','padStartBase','padStartData','padLvlIds','padParmParse','padLvlFunVar','padLvlFun','padSource','padOrg','padPrefix','padParms','padTagSeq','padPipeBefore','padPipeAfter', 'padSelect'
  ] );

  define ( 'padStrSto', ['padDataStore','padContentStore','padBoolStore','pqStore'] );
  define ( 'padStrDat', ['padData','padCurrent','padSetLvl','padSetOcc','padPrm','padOpt'] );

  define ( 'padOptionsStart', ['track', 'before', 'dedup', 'page', 'sort', 'ignore', 'print', 'parent', 'trace', 'pre'] );

  define ( 'padOptionsEnd', ['toBool', 'toContent', 'toData', 'tidy', 'dump'] );

  define ( 'PQ', PAD . 'sequence/'      );
  define ( 'PT', PQ .  'types/'         );
  define ( 'PA', PQ .  'actions/types/' );

?>