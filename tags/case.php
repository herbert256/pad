<?php

  $padTst     = '{when';
  $padBasis   = padEval  ($padPrm [$pad] [0]);        
  $padChk     = strpos   ($padContent , $padTst);
  $padPos     = strpos   ($padContent, '}', $padChk);
  $padEval    = substr   ($padContent, $padChk+6, $padPos-($padChk+6));
  $padContent = substr   ($padContent, $padPos+1);

  return include 'go/if_case.php';

?>