<?php

  $pTst     = '{when';
  $pBasis   = pEval ($pPrms[$p]);        
  $pChk     = strpos   ($pContent , $pTst);
  $pPos     = strpos   ($pContent, '}', $pChk);
  $pEval    = substr   ($pContent, $pChk+6, $pPos-($pChk+6));
  $pContent = substr   ($pContent, $pPos+1);

  return include 'go/if_case.php';

?>