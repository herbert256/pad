<?php

  // {case $value}{when 'a'}...{when 'b', 'c'}...{/case} keeps only the branch whose {when}
  // matches - any of the values a when lists, padCaseWhen in tags/_lib/case.php.
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
  //
  // Only a value written out - a quoted string, a number, true or false (padMetaLiteral) -
  // can be written twice. A {when} holding a field, a call or an operator answers what it
  // answers for this row: comparing those values made the same page pass for one row and
  // die on the next - {case true}{when $n gt 100}...{when $n gt 10}... was "the {when '1'}
  // is written twice" for n=500 and "the {when ''}" for n=5, and {when $home}{when $work}
  // when both held the same town. Nor is such a {when} evaluated here: the match below
  // evaluates the ones it reaches, and a call in one behind the answering branch ran here
  // although it is never reached.

  if ( $padCheckSyntax ) {

    $padCaseElse = strpos ( $padContent, '{else}' );

    while ( $padCaseElse !== FALSE and ! padElseFree ( substr ( $padContent, 0, $padCaseElse ) ) )
      $padCaseElse = strpos ( $padContent, '{else}', $padCaseElse+6 );

    // A second {else} of our own: what follows it never renders.

    if ( $padCaseElse !== FALSE )
      for ( $padCaseAt = strpos ( $padContent, '{else}', $padCaseElse+6 ); $padCaseAt !== FALSE; $padCaseAt = strpos ( $padContent, '{else}', $padCaseAt+6 ) )
        if ( padElseFree ( substr ( $padContent, 0, $padCaseAt ) ) )
          padError ( "the {case} has two {else}s - what follows the second never renders" );

    $padCaseSeen = [];
    $padCaseScan = 0;

    while ( ( $padCaseScan = strpos ( $padContent, '{when', $padCaseScan ) ) !== FALSE ) {

      if ( padCheckTag ( 'case', substr ( $padContent, 0, $padCaseScan ) ) ) {

        if ( $padCaseElse !== FALSE and $padCaseScan > $padCaseElse )
          padError ( "a {when} behind the {else} never answers" );

        $padCaseText = padBranchText ( $padContent, $padCaseScan, 5, $padCasePos );

        padBranchNested ( $padCaseText, 'a {when}', "{when \$y}" );

        foreach ( padParseOptions ( $padCaseText ) as $padCaseOne ) {

          if ( padMetaLiteral ( $padCaseOne ) === NULL )
            continue;

          $padCaseVal = padEval ( $padCaseOne );

          if ( in_array ( $padCaseVal, $padCaseSeen, TRUE ) )
            padError ( "the {when '" . $padCaseVal . "'} is written twice - the first one answers" );

          $padCaseSeen [] = $padCaseVal;

        }

      }

      $padCaseScan += 5;

    }

  }

  $padIf      = padBranchText ( $padContent, $padChk, 5, $padPos );

  if ( $padCheckSyntax and trim ( $padIf ) == '' )
    padError ( "a {when} of this {case} has no value" );

  padBranchNested ( $padIf, 'a {when}', "{when \$y}" );

  $padContent = substr   ( $padContent, $padPos+1 );
  $padChk     = strpos   ( $padContent, '{when' );

  // Under a coverage recording the branch that answers is noted (lib/coverage.php):
  // 'when <values>', 'else', or 'none' when nothing matched and there is no {else}.

  $padCaseArm = "when $padIf";

  while ($padChk !== FALSE) {

    if ( ! padCheckTag  ('case', substr ( $padContent, 0, $padChk ) ) )

      $padChk = strpos ( $padContent , '{when', $padChk+5 );

    elseif ( padCaseWhen ( $padBasis, $padIf ) ) {

      $padContent = substr ( $padContent, 0, $padChk );

      if ( $padCoverageRun )
        padCoverageArm ( $padCaseArm );

      return TRUE;

    } else {

      $padIf      = padBranchText ( $padContent, $padChk, 5, $padPos );
      $padCaseArm = "when $padIf";

      if ( $padCheckSyntax and trim ( $padIf ) == '' )
        padError ( "a {when} of this {case} has no value" );

      padBranchNested ( $padIf, 'a {when}', "{when \$y}" );

      $padContent = substr   ( $padContent, $padPos+1 );
      $padChk     = strpos   ( $padContent, '{when' );

    }

  }

  // An {else} of our own ends the last branch and opens the default one, the same way
  // {if} takes it: the part in front of it when that last {when} matches, the part after it
  // when nothing has matched at all. padElseFree skips an {else} belonging to a nested tag -
  // a {case}, an {if} or a condition tag, the other owners of an {else}: a branch holding
  // {if}...{else}...{/if} was split at the if's {else}, and in the strict pre-scan above
  // every {when} after it stood 'behind the {else}'.

  $padChk = strpos ( $padContent, '{else}' );

  while ( $padChk !== FALSE and ! padElseFree ( substr ( $padContent, 0, $padChk ) ) )
    $padChk = strpos ( $padContent, '{else}', $padChk+6 );

  if ( $padChk !== FALSE ) {

    if ( padCaseWhen ( $padBasis, $padIf ) )
      $padContent = substr ( $padContent, 0, $padChk );
    else {
      $padContent = substr ( $padContent, $padChk+6 );
      $padCaseArm = 'else';
    }

    if ( $padCoverageRun )
      padCoverageArm ( $padCaseArm );

    return TRUE;

  }

  $padCaseHeld = padCaseWhen ( $padBasis, $padIf );

  if ( $padCoverageRun )
    padCoverageArm ( $padCaseHeld ? $padCaseArm : 'none' );

  return $padCaseHeld;

?>
