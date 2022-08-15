<?php
  
  $padChk = strpos ($padContent, $padTst);
  $padAdd = strlen ($padTst);
  
  while ($padChk !== FALSE) {

    if ( ! pCheckTag ($padTag [$pad], substr($padContent, 0, $padChk)) ) 

      $padChk = strpos($padContent , $padTst, $padChk+$padAdd);

    else {

      $padEval = pEval($padEval);

      if ( ($padTag [$pad] == 'if' and $padEval) or ($padTag [$pad] == 'case' and $padBasis == $padEval) ) {
        $padContent = substr ($padContent, 0, $padChk);
        return TRUE;
      }

      $padPos     = strpos($padContent, '}', $padChk); 
      $padEval    = substr($padContent, $padChk+$padAdd+1, $padPos-($padChk+$padAdd+1));
      $padContent = substr($padContent, $padPos+1);
      $padChk     = strpos($padContent, $padTst);

    }
 
  }

  $padEval = pEval($padEval);

  return ( ($padTag [$pad] == 'if' and $padEval) or ($padTag [$pad] == 'case' and $padBasis == $padEval) );

?>