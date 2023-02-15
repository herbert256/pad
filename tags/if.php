<?php
  
  $padEval = $padPrm [$pad] [0];
  $padChk  = strpos ($padContent, '{elseif');

  while ($padChk !== FALSE) {

    if ( ! padCheckTag ($padTag [$pad], substr($padContent, 0, $padChk)) ) 

      $padChk = strpos($padContent , '{elseif', $padChk+7);

    else {

      if ( padEval($padEval) )  {
        $padContent = substr ($padContent, 0, $padChk);
        return TRUE;
      }

      $padPos     = strpos($padContent, '}', $padChk); 
      $padEval    = substr($padContent, $padChk+7+1, $padPos-($padChk+7+1));
      $padContent = substr($padContent, $padPos+1);
      $padChk     = strpos($padContent, '{elseif');

    }
 
  }

  return ( padEval($padEval) ) ? TRUE : FALSE;

?>