<?php

  // The engine's compile-time constants, defined once per request (include_once from
  // inits/inits.php, before anything else).
  //
  // padLevelVars is the master list of every global that is indexed by the nesting level
  // $pad. padStrPad (lib/checks.php) reads it to leave these arrays out of the engine
  // snapshot a nested pass takes and restores - a pass opens its levels above the current
  // one, so the slots below stay as they were - and the dump files them under the levels.
  // A per-level global left off it is snapshotted and restored wholesale instead, and a
  // sandboxed pass that created it unsets it afterwards.
  //
  // It had drifted: sixteen names nothing uses any more, padXmlLevel for what is
  // padInfoXmlLevel, and padWhileRound and padOptionsAppStartCall missing.
  //
  // padStrSto and padStrDat name the stores and the data-carrying level arrays that the
  // string/store machinery has to treat specially. padOptionsStart and padOptionsEnd list
  // the tag options handled before and after a level's content is produced.
  //
  // PQ, PT and PA are the sequence subsystem's path shorthands, the counterparts of PAD.

  define ( 'padLevelVars', [
    'padTag', 'padType', 'padPair', 'padPrm', 'padName', 'padData', 'padCurrent',
    'padWalk', 'padWalkData', 'padDone', 'padOccur', 'padStart', 'padEnd', 'padBase',
    'padOut', 'padResult', 'padHit', 'padNull', 'padElse', 'padArray', 'padAfter',
    'padBefore', 'padPrmType', 'padGiven', 'padOpt', 'padOptionsAppStart', 'padSaveLvl',
    'padSaveOcc', 'padSetLvl', 'padSetOcc', 'padDeleteOcc', 'padDeleteLvl', 'padPageApp',
    'padKey', 'padInfoTraceIds', 'padInfoTraceOccurId', 'padInfoTraceLevelChilds',
    'padInfoTraceOccurChilds', 'padInfoTraceOccur', 'padInfoTraceLevel', 'padAfterBase',
    'padBeforeBase', 'padEndBase', 'padOccurStart', 'padStartBase', 'padStartData',
    'padParmParse', 'padLvlFunVar', 'padLvlFun', 'padSource', 'padOrg', 'padPrefix',
    'padParms', 'padTagSeq', 'padPipeBefore', 'padPipeAfter', 'padSelect', 'padBaseValue',
    'padAtTag', 'padScan', 'padInfoXmlLevel', 'padWhileRound', 'padOptionsAppStartCall'
  ] );

  define ( 'padStrSto', ['padDataStore','padContentStore','padBoolStore','pqStore'] );
  define ( 'padStrDat', ['padData','padCurrent','padSetLvl','padSetOcc','padPrm','padOpt'] );

  define ( 'padOptionsStart', ['track', 'before', 'dedup', 'page', 'sort', 'ignore', 'print', 'parent', 'trace', 'pre'] );

  define ( 'padOptionsEnd', ['toBool', 'toContent', 'toData', 'tidy', 'dump'] );

  define ( 'PQ', PAD . 'sequence/'      );
  define ( 'PT', PQ .  'types/'         );
  define ( 'PA', PQ .  'actions/types/' );

?>