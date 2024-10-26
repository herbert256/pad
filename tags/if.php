<?php
  
  $padIf  = '"' . $padParm . '"';
  $padChk = strpos ($padContent, '{elseif');

  while ($padChk !== FALSE) {

    if ( ! padCheckTag ('if', substr($padContent, 0, $padChk)) ) 
      
      $padChk = strpos($padContent , '{elseif', $padChk+7);

    else {

      if ( padEval($padIf) )
        return substr ($padContent, 0, $padChk);

      $padPos     = strpos($padContent, '}', $padChk); 
      $padIf      = substr($padContent, $padChk+8, $padPos-($padChk+8));
      $padContent = substr($padContent, $padPos+1);
      $padChk     = strpos($padContent, '{elseif');

    }
 
  }

  return ( padEval($padIf) ) ? TRUE : FALSE;

?>