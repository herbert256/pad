<?php
  
  $padP = $pad;
  
  $pad++;  

  $padTag         [$pad] = $padTypeCheck  ?? 'n/a';
  $padType        [$pad] = $padTypeResult ?? 'n/a';
  $padTagParm     [$pad] = $padTypeParm   ?? 'n/a';
  $padGiven       [$pad] = $padTypeGiven  ?? FALSE;

  $padPair        [$pad] = FALSE;

  $padTrue        [$pad] = '';
  $padFalse       [$pad] = '';

  $padPrm         [$pad] = []; 
  $padPrmType     [$pad] = '';

  $padName        [$pad] = '';

  $padData        [$pad] = padDefaultData ();
  $padCurrent     [$pad] = [];
  $padKey         [$pad] = 1;
  $padDefault     [$pad] = TRUE;

  $padWalk        [$pad] = 'start';

  $padWalkData    [$pad] = [];
  
  $padDone        [$pad] = [];
  $padOccur       [$pad] = 0;
  $padStart       [$pad] = 0;
  $padEnd         [$pad] = 0;

  $padBase        [$pad] = '';
  $padHtml        [$pad] = '';
  $padResult      [$pad] = '';
 
  $padHit         [$pad] = FALSE;
  $padNull        [$pad] = FALSE;
  $padElse        [$pad] = FALSE;
  $padArray       [$pad] = FALSE;
  $padText        [$pad] = FALSE;

  $padLevelDir    [$pad] = $padOccurDir [$pad-1] ?? $padTraceDir;
  $padOccurDir    [$pad] = $padLevelDir [$pad];

  $padSaveVars   [$pad] = [];
  $padDeleteVars [$pad] = [];
  $padSet        [$pad] = [];
  $padEndOptions [$pad] = [];

  $padTagCnt     [$pad] = 0;

  $padAfter      [$pad] = 0;
  $padBefore     [$pad] = 0;
  $padBeforeData [$pad] = '';

  $padSetName = '';
  
?>