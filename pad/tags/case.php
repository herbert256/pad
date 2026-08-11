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

  // A {case} without its {/case} used to fall through as a single tag. Strict mode says
  // what is missing.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  if ( $padCheckSyntax and trim ( $padParms [$pad] [0] ['padPrmOrg'] ?? '' ) == '' )
    padError ( "the {case} has no value" );

  $padBasis   = padEval  ( $padParms [$pad] [0] ['padPrmOrg'] ?? '' );
  $padChk     = strpos   ( $padContent , '{when' );

  if ( $padCheckSyntax and $padChk === FALSE )
    padError ( "the {case} has no {when}" );

  if ( $padCheckSyntax and trim ( substr ( $padContent, 0, $padChk ) ) != '' )
    padError ( "what stands before the first {when} of a {case} never renders" );

  // Strict pre-scan of the branches: a {when} standing behind the {else} never answers,
  // and a {when} repeating an earlier value never answers either - the first one does.

  if ( $padCheckSyntax ) {

    $padCaseElse = strpos ( $padContent, '{else}' );

    while ( $padCaseElse !== FALSE and ! padCheckTag ( 'case', substr ( $padContent, 0, $padCaseElse ) ) )
      $padCaseElse = strpos ( $padContent, '{else}', $padCaseElse+6 );

    $padCaseSeen = [];
    $padCaseScan = 0;

    while ( ( $padCaseScan = strpos ( $padContent, '{when', $padCaseScan ) ) !== FALSE ) {

      if ( padCheckTag ( 'case', substr ( $padContent, 0, $padCaseScan ) ) ) {

        if ( $padCaseElse !== FALSE and $padCaseScan > $padCaseElse )
          padError ( "a {when} behind the {else} never answers" );

        $padCasePos = strpos ( $padContent, '}', $padCaseScan );
        $padCaseVal = padEval ( substr ( $padContent, $padCaseScan+6, $padCasePos-($padCaseScan+6) ) );

        if ( in_array ( $padCaseVal, $padCaseSeen, TRUE ) )
          padError ( "the {when '" . $padCaseVal . "'} is written twice - the first one answers" );

        $padCaseSeen [] = $padCaseVal;

      }

      $padCaseScan += 5;

    }

  }

  $padPos     = strpos   ( $padContent, '}', $padChk );
  $padIf      = substr   ( $padContent, $padChk+6, $padPos-($padChk+6) );

  if ( $padCheckSyntax and trim ( $padIf ) == '' )
    padError ( "a {when} of this {case} has no value" );

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

      if ( $padCheckSyntax and trim ( $padIf ) == '' )
        padError ( "a {when} of this {case} has no value" );

      $padContent = substr   ( $padContent, $padPos+1 );
      $padChk     = strpos   ( $padContent, '{when' );

    }

  }

  // An {else} of our own ends the last branch and opens the default one, the same way
  // {if} takes it: the part in front of it when that last {when} matches, the part after it
  // when nothing has matched at all. padCheckTag skips an {else} belonging to a nested tag.

  $padChk = strpos ( $padContent, '{else}' );

  while ( $padChk !== FALSE and ! padCheckTag ( 'case', substr ( $padContent, 0, $padChk ) ) )
    $padChk = strpos ( $padContent, '{else}', $padChk+6 );

  if ( $padChk !== FALSE ) {

    if ( $padBasis == padEval ( $padIf ) )
      $padContent = substr ( $padContent, 0, $padChk );
    else
      $padContent = substr ( $padContent, $padChk+6 );

    return TRUE;

  }

  return $padBasis == padEval ( $padIf );

?>