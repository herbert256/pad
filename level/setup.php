<?php
  
  $pad++;  
  $padLvlId++;

  $padLvlIds     [$pad] = $padLvlId;

  $padParmParse  [$pad] = [];
  
  $padOpt        [$pad] = [];
  $padPrm        [$pad] = [];

  $padSetLvl     [$pad] = [];
  $padSetOcc     [$pad] = [];

  $padOpt        [$pad] [0] = $padTagOpts;
  $padOpt        [$pad] [1] = '';

  $padTag        [$pad] = $padTypeCheck  ?? '';
  $padType       [$pad] = $padTypeResult ?? '';
  $padGiven      [$pad] = $padTypeGiven  ?? '';
  $padPair       [$pad] = $padPairSet    ?? '';
  $padBase       [$pad] = $padBaseSet    ?? '';
  $padPrmType    [$pad] = $padPrmTypeSet ?? '';

  $padName       [$pad] = '';

  $padData       [$pad] = padDefaultData ();
  $padCurrent    [$pad] = [];
  $padKey        [$pad] = 1;

  $padWalk       [$pad] = 'start';
  $padWalkData   [$pad] = [];

  $padDone       [$pad] = [];
  $padOccur      [$pad] = 0;
  $padStart      [$pad] = 0;
  $padEnd        [$pad] = 0;

  $padPad        [$pad] = '';
  $padResult     [$pad] = '';
 
  $padHit        [$pad] = FALSE;
  $padNull       [$pad] = FALSE;
  $padElse       [$pad] = FALSE;
  $padArray      [$pad] = FALSE;
  $padCount      [$pad] = FALSE;

  $padSaveLvl    [$pad] = [];
  $padDeleteLvl  [$pad] = [];

  $padSaveOcc    [$pad] = [];
  $padDeleteOcc  [$pad] = [];
  
  $padEndBase    [$pad] = '';
  $padStartBase  [$pad] = [];
  $padStartData  [$pad] = [];

  $padTable      [$pad] = [];
  $padTableTag   [$pad] = '';

  $padLvlFun     [$pad] = FALSE;
  $padLvlFunVar  [$pad] = [];

  $padOccurStart [$pad] = [];

  $padOptionsAppStart [$pad] = [];

  $padForceTagName  = '';
  $padForceDataName = '';
  $padFalse         = '';  

  if ( $GLOBALS['padInfo'] )
    include '/pad/info/events/setup.php';

?>