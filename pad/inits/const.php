<?php

  // The engine's compile-time constants, defined once per request (include_once from
  // inits/inits.php, before anything else).
  //
  // padLevelVars is the master list of every global that is indexed by the nesting level
  // $pad; level/ and start/page.php walk it to save, restore and clear a level in one go, so
  // a new per-level global only becomes real once its name is added here.
  //
  // padStrSto and padStrDat name the stores and the data-carrying level arrays that the
  // string/store machinery has to treat specially. padOptionsStart and padOptionsEnd list
  // the tag options handled before and after a level's content is produced.
  //
  // PQ, PT and PA are the sequence subsystem's path shorthands, the counterparts of PAD.

  define ( 'padLevelVars', [ 'padTag','padType','padPair','padPrm','padName','padData','padCurrent','padWalk','padWalkData','padDone','padOccur','padStart','padEnd','padBase','padOut','padResult','padHit','padNull','padElse','padArray','padSaveVars','padDeleteVars','padSetSave','padSetDelete','padTagCnt','padAfter','padBefore','padBeforeData','padPrmType','padSet','padGiven','padDeleteSet','padOpt','padOptionsAppStart','padSaveSet','padSaveLvl','padSaveOcc','padSetLvl','padSetOcc','padDeleteOcc','padDeleteLvl','padPagePad','padPageApp','padKey','padInfoTraceIds','padInfoTraceOccurId','padInfoTraceLevelChilds','padInfoTraceOccurChilds','padInfoTraceOccur','padInfoTraceLevel','padInfoTraceOccurTag','padInfoTraceOccOpen','padInfoTraceOccClose','padXmlLevel','padAfterBase','padBeforeBase','padDouble','padEndBase','padOccurStart','padOccurType','padStartBase','padStartData','padParmParse','padLvlFunVar','padLvlFun','padSource','padOrg','padPrefix','padParms','padTagSeq','padPipeBefore','padPipeAfter', 'padSelect'
  ] );

  define ( 'padStrSto', ['padDataStore','padContentStore','padBoolStore','pqStore'] );
  define ( 'padStrDat', ['padData','padCurrent','padSetLvl','padSetOcc','padPrm','padOpt'] );

  define ( 'padOptionsStart', ['track', 'before', 'dedup', 'page', 'sort', 'ignore', 'print', 'parent', 'trace', 'pre'] );

  define ( 'padOptionsEnd', ['toBool', 'toContent', 'toData', 'tidy', 'dump'] );

  define ( 'PQ', PAD . 'sequence/'      );
  define ( 'PT', PQ .  'types/'         );
  define ( 'PA', PQ .  'actions/types/' );

?>