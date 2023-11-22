<?php
  
  $pad++;  

  $padOpt        [$pad] = [];
  $padPrm        [$pad] = [];

  $padSetLvl     [$pad] = [];
  $padSetOcc     [$pad] = [];

  $padOpt        [$pad] [0] = trim($padWords[1] ?? '');
  $padOpt        [$pad] [1] = '';

  $padTag        [$pad] = $padTypeCheck  ?? '';
  $padType       [$pad] = $padTypeResult ?? '';
  $padGiven      [$pad] = $padTypeGiven  ?? '';
  $padPair       [$pad] = $padPairSet    ?? '';
  $padTrue       [$pad] = $padTrueSet    ?? '';
  $padPrmType    [$pad] = $padPrmTypeSet ?? '';

  $padFalse      [$pad] = '';  
  $padName       [$pad] = '';

  $padData       [$pad] = padDefaultData ();
  $padDefault    [$pad] = TRUE;
  $padCurrent    [$pad] = [];
  $padKey        [$pad] = 1;

  $padWalk       [$pad] = 'start';
  $padWalkData   [$pad] = [];

  $padDone       [$pad] = [];
  $padOccurType  [$pad] = '';
  $padOccur      [$pad] = 0;
  $padStart      [$pad] = 0;
  $padEnd        [$pad] = 0;

  $padPadStart   [$pad] = '';
  $padBase       [$pad] = '';
  $padPad        [$pad] = '';
  $padResult     [$pad] = '';
 
  $padHit        [$pad] = FALSE;
  $padNull       [$pad] = FALSE;
  $padElse       [$pad] = FALSE;
  $padArray      [$pad] = FALSE;
  $padCount      [$pad] = FALSE;
  $padText       [$pad] = FALSE;

  $padDouble     [$pad] = '';

  $padSaveLvl    [$pad] = [];
  $padDeleteLvl  [$pad] = [];

  $padSaveOcc    [$pad] = [];
  $padDeleteOcc  [$pad] = [];
  
  $padAfterBase  [$pad] = '';
  $padBeforeBase [$pad] = [];
  $padBeforeData [$pad] = [];

  $padEndBase    [$pad] = '';
  $padStartBase  [$pad] = [];
  $padStartData  [$pad] = [];

  $padTable      [$pad] = [];
  $padTableTag   [$pad] = '';

  $padOccurStart [$pad] = [];

  $padOptionsAppStart [$pad] = [];
  $padOptionsCallback [$pad] = [];

  $padForceTagName  = '';
  $padForceDataName = '';

  if ( $padTraceActive ) include pad . 'tail/types/trace/level/start.php';
  if ( $padXml         ) include pad . 'tail/types/xml/level/start.php';  
  if ( $padXref        ) include pad . 'tail/types/xref/items/tag.php';

?>