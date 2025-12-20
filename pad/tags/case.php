<?php

  $padBasis   = padEval  ( $padParms [$pad] [0] ['padPrmOrg'] );
  $padChk     = strpos   ( $padContent , '{when' );
  $padPos     = strpos   ( $padContent, '}', $padChk );
  $padIf      = substr   ( $padContent, $padChk+6, $padPos-($padChk+6) );
  $padContent = substr   ( $padContent, $padPos+1 );
  $padChk     = strpos   ( $padContent, '{when' );

  while ($padChk !== FALSE) {

    if ( ! padCheckTag  ('case', substr ( $padContent, 0, $padChk ) ) )

      $padChk = strpos ( $padContent , '{when', $padChk+5 );

    elseif ( $padBasis == padEval ( $padIf ) ) {

      $padContent = substr ( $padContent, 0, $padChk );

      return TRUE;

    } else {

      $padPos     = strpos   ( $padContent, '}', $padChk );
      $padIf      = substr   ( $padContent, $padChk+6, $padPos-($padChk+6) );
      $padContent = substr   ( $padContent, $padPos+1 );
      $padChk     = strpos   ( $padContent, '{when' );

    }

  }

  return $padBasis == padEval ( $padIf );

?>