<?php
  
  $pad++;  

  if ($padHistory)
    include PAD . 'history/setup.php';

  $padOpt        [$pad] = [];
  $padSet        [$pad] = [];
  $padPrm        [$pad] = [];

  $padOpt        [$pad] [0] = trim($padWords[1] ?? '');
  $padOpt        [$pad] [1] = '';

  $padTag        [$pad] = $padTypeCheck  ?? '';
  $padType       [$pad] = $padTypeResult ?? '';
  $padTagParm    [$pad] = $padTypeParm   ?? '';
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
  $padOccur      [$pad] = 0;
  $padStart      [$pad] = 0;
  $padEnd        [$pad] = 0;

  $padBase       [$pad] = '';
  $padHtml       [$pad] = '';
  $padResult     [$pad] = '';
 
  $padHit        [$pad] = FALSE;
  $padNull       [$pad] = FALSE;
  $padElse       [$pad] = FALSE;
  $padArray      [$pad] = FALSE;
  $padText       [$pad] = FALSE;

  $padLevelDir   [$pad] = $padOccurDir [$pad-1] ?? $padTraceDir;
  $padOccurDir   [$pad] = $padLevelDir [$pad];

  $padSaveVars   [$pad] = [];
  $padDeleteVars [$pad] = [];

  $padEndOptions [$pad] = [];
  $padOptionsApp [$pad] = [];
  
  $padTagCnt     [$pad] = 0;

  $padAfter      [$pad] = 0;
  $padBefore     [$pad] = 0;
  $padBeforeData [$pad] = '';

  $padParseLevel [$pad] = $padParseLevel [$pad] ?? '';
  $padParseFalse [$pad] = $padParseFalse [$pad] ?? '';
  $padParseInfo  [$pad] = $padParseInfo [$pad] ?? '';

  $padForceName = '';
  
?>