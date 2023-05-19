<?php
  
   $padLevelVars = [ 'padTag','padType','padPair','padTrue','padFalse','padPrm','padName','padData','padCurrent','padDefault','padWalk','padWalkData','padDone','padOccur','padStart','padEnd','padBase','padHtml','padResult','padHit','padNull','padElse','padArray','padText','padSaveVars','padDeleteVars','padSetSave','padSetDelete','padTagCnt','padAfter','padBefore','padBeforeData','padPrmType','padSet','padGiven','padDeleteSet','padOpt','padOptionsApp','padSaveSet','padTable','padTableTag'];

  $GLOBALS ['padFunctionSave'] [$GLOBALS['pad']] = [];

  foreach ($GLOBALS as $padK => $padV )
    if ( substr($padK, 0, 3) == 'pad' and ! in_array($padK, $padLevelVars) 
         and $padK <> 'padFunctionSave' and $padK <> 'padK' and $padK <> 'padV' ) 
      $GLOBALS ['padFunctionSave'] [$GLOBALS['pad']] [$padK] = $GLOBALS [$padK];

?>