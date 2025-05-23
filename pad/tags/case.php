<?php

  $padBasis   = padEval  ( $padParms [$pad] [0] ['padPrmOrg'] );        
  $padChk     = strpos   ( $padContent , '{when' );
  
  $padPos     = strpos   ( $padContent, '}', $padChk );
  $padIf      = substr   ( $padContent, $padChk+6, $padPos-($padChk+6) );
  $padContent = substr   ( $padContent, $padPos+1 );
  $padChk     = strpos   ( $padContent, '{when' );
  
  while ($padChk !== FALSE) {

    if ( ! padCheckTag ('case', substr($padContent, 0, $padChk)) ) 

      $padChk = strpos ($padContent , '{when', $padChk+5);

    elseif ( $padBasis == padEval($padIf) ) 

      return include 'tags/go/case.php';

    else {

      $padPos     = strpos($padContent, '}', $padChk); 
      $padIf      = substr($padContent, $padChk+6, $padPos-($padChk+6));
      $padContent = substr($padContent, $padPos+1);
      $padChk     = strpos($padContent, '{when');

    }
 
  }

  return include 'tags/go/if.php';

?>