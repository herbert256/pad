<?php

  $padBasis   = padEval  ($padPrm [$pad] [0]);        
  $padChk     = strpos   ($padContent , '{when');
  
  $padPos     = strpos   ($padContent, '}', $padChk);
  $padEval    = substr   ($padContent, $padChk+6, $padPos-($padChk+6));
  $padContent = substr   ($padContent, $padPos+1);
  $padChk     = strpos   ($padContent, '{when');
  
  while ($padChk !== FALSE) {

    if ( ! padCheckTag ($padTag [$pad], substr($padContent, 0, $padChk)) ) 

      $padChk = strpos($padContent , '{when', $padChk+5);

    else {

      if ( $padBasis == padEval($padEval) ) {
        $padContent = substr ($padContent, 0, $padChk);
        return TRUE;
      }

      $padPos     = strpos($padContent, '}', $padChk); 
      $padEval    = substr($padContent, $padChk+6, $padPos-($padChk+6));
      $padContent = substr($padContent, $padPos+1);
      $padChk     = strpos($padContent, '{when');

    }
 
  }

  return ( $padBasis == padEval($padEval) );

?>