<?php
  
  $pChk = strpos ($pContent, $pTst);
  $pAdd = strlen ($pTst);
  
  while ($pChk !== FALSE) {

    if ( ! pCheckTag ($pTag [$p], substr($pContent, 0, $pChk)) ) 

      $pChk = strpos($pContent , $pTst, $pChk+$pAdd);

    else {

      $pEval = pEval($pEval);

      if ( ($pTag [$p]== 'if' and $pEval) or ($pTag [$p]== 'case' and $pBasis == $pEval) ) {
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

  return ( ($pTag [$p]== 'if' and $pEval) or ($pTag [$p]== 'case' and $pBasis == $pEval) );

?>