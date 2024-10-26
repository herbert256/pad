<?php

  $padBasis   = padEval  ($padOpt [$pad] [0]);        
  $padChk     = strpos   ($padContent , '{when');
  $padPos     = strpos   ($padContent, '}', $padChk);
  $padCase    = substr   ($padContent, $padChk+6, $padPos-($padChk+6));
  $padContent = substr   ($padContent, $padPos+1);
  $padChk     = strpos   ($padContent, '{when');
  
  while ($padChk !== FALSE) {

    if ( ! padCheckTag ('case', substr($padContent, 0, $padChk)) ) 

      $padChk = strpos($padContent , '{when', $padChk+5);

    elseif ( $padBasis == padEval($padCase) ) 
        
      return substr ($padContent, 0, $padChk);

     else {

      $padPos     = strpos($padContent, '}', $padChk); 
      $padCase    = substr($padContent, $padChk+6, $padPos-($padChk+6));
      $padContent = substr($padContent, $padPos+1);
      $padChk     = strpos($padContent, '{when');

    }
 
  }

  return ( $padBasis == padEval($padCase) );

?>