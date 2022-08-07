<?php
  
  $pChk = strpos ($pContent, $pTst);
  $pAdd = strlen ($pTst);
  
  while ($pChk !== FALSE) {

    if ( ! pCheck_tag ($pTag, substr($pContent, 0, $pChk)) ) 

      $pChk = strpos($pContent , $pTst, $pChk+$pAdd);

    else {

      $pEval = pEval($pEval);

      if ( ($pTag == 'if' and $pEval) or ($pTag == 'case' and $pBasis == $pEval) ) {
        $pContent = substr ($pContent, 0, $pChk);
        return TRUE;
      }

      $pPos     = strpos($pContent, '}', $pChk); 
      $pEval    = substr($pContent, $pChk+$pAdd+1, $pPos-($pChk+$pAdd+1));
      $pContent = substr($pContent, $pPos+1);
      $pChk     = strpos($pContent, $pTst);

    }
 
  }

  $pEval = pEval($pEval);

  return ( ($pTag == 'if' and $pEval) or ($pTag == 'case' and $pBasis == $pEval) );

?>