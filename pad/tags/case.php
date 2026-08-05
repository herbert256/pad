<?php

  // {case $value}{when 'a'}...{when 'b'}...{/case} keeps only the branch whose {when}
  // matches.
  //
  // The branches are not levels of their own: the tag chops its content up itself. The
  // first parameter is evaluated from its unparsed text to give the basis, then each
  // {when ...} in turn is cut off the front of $padContent, leaving that branch at the
  // front; padCheckTag() makes a {when} belonging to a nested {case} be skipped. A match
  // trims $padContent to that branch and returns TRUE, so the level renders it; the last
  // branch is left standing and the final comparison decides whether it survives.

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