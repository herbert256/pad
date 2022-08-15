<?php
  
  $padP = $pad;
  
  $pad++;  

  $padTag         [$pad] = 'true';
  $padType        [$pad] = 'true';

  $padPair        [$pad] = FALSE;

  $padTrue        [$pad] = '';
  $padFalse       [$pad] = '';

  $padPrm         [$pad] = ''; 
  $padPrms        [$pad] = '';
  $padPrmsType    [$pad] = '';
  $padPrmsTag     [$pad] = [];
  $padPrmsVal     [$pad] = [];

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
  $padSetSave    [$pad] = [];
  $padSetDelete  [$pad] = [];

  $padTagCnt      [$pad] = 0;

?>