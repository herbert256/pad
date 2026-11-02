<?php

  // {pad:case $value}{pad:when 'a'}...{pad:when 'b', 'c'}...{/pad:case} keeps only the branch whose {pad:when}
  // matches - any of the values a when lists, padCaseWhen in tags/_lib/case.php.
  //
  // The branches are not levels of their own: the tag chops its content up itself. The
  // first parameter is evaluated from its unparsed text to give the basis, then each
  // {pad:when ...} in turn is cut off the front of $padContent, leaving that branch at the
  // front; padCheckTag() makes a {pad:when} belonging to a nested {pad:case} be skipped. A match
  // trims $padContent to that branch and returns TRUE, so the level renders it; the last
  // branch is left standing and the final comparison decides whether it survives.

  // A {pad:case} without its {/pad:case} used to fall through as a single tag. Strict mode says
  // what is missing.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  $padCaseOrg = padFirstParm ();

  if ( $padCheckSyntax and trim ( $padCaseOrg ) == '' )
    padError ( "the {pad:case} has no value" );

  $padBasis   = padEval  ( $padCaseOrg );
  $padChk     = strpos   ( $padContent , '{pad:when' );

  if ( $padCheckSyntax and $padChk === FALSE )
    padError ( "the {pad:case} has no {pad:when}" );

  if ( $padCheckSyntax and trim ( substr ( $padContent, 0, $padChk ) ) != '' )
    padError ( "what stands before the first {pad:when} of a {pad:case} never renders" );

  // Strict pre-scan of the branches: a {pad:when} standing behind the {pad:else} never answers,
  // and a {pad:when} repeating an earlier value never answers either - the first one does.
  //
  // Only a value written out - a quoted string, a number, true or false (padMetaLiteral) -
  // can be written twice. A {pad:when} holding a field, a call or an operator answers what it
  // answers for this row: comparing those values made the same page pass for one row and
  // die on the next - {pad:case true}{pad:when $n gt 100}...{pad:when $n gt 10}... was "the {pad:when '1'}
  // is written twice" for n=500 and "the {pad:when ''}" for n=5, and {pad:when $home}{pad:when $work}
  // when both held the same town. Nor is such a {pad:when} evaluated here: the match below
  // evaluates the ones it reaches, and a call in one behind the answering branch ran here
  // although it is never reached.

  if ( $padCheckSyntax ) {

    $padCaseElse = strpos ( $padContent, '{pad:else}' );

    while ( $padCaseElse !== FALSE and ! padElseFree ( substr ( $padContent, 0, $padCaseElse ), $padContent ) )
      $padCaseElse = strpos ( $padContent, '{pad:else}', $padCaseElse+10 );

    // A second {pad:else} of our own: what follows it never renders.

    if ( $padCaseElse !== FALSE )
      for ( $padCaseAt = strpos ( $padContent, '{pad:else}', $padCaseElse+10 ); $padCaseAt !== FALSE; $padCaseAt = strpos ( $padContent, '{pad:else}', $padCaseAt+10 ) )
        if ( padElseFree ( substr ( $padContent, 0, $padCaseAt ), $padContent ) )
          padError ( "the {pad:case} has two {pad:else}s - what follows the second never renders" );

    $padCaseSeen = [];
    $padCaseScan = 0;

    while ( ( $padCaseScan = strpos ( $padContent, '{pad:when', $padCaseScan ) ) !== FALSE ) {

      if ( padCheckTag ( 'case', substr ( $padContent, 0, $padCaseScan ), $padContent ) ) {

        if ( $padCaseElse !== FALSE and $padCaseScan > $padCaseElse )
          padError ( "a {pad:when} behind the {pad:else} never answers" );

        $padCaseText = padBranchText ( $padContent, $padCaseScan, 9, $padCasePos );

        padBranchNested ( $padCaseText, 'a {pad:when}', "{pad:when \$y}" );

        // Two values are the same when the match would take them for the same: it compares
        // with ==, so 1, '01' and 1.0 are one number, and a {pad:when '01'} behind a {pad:when 1}
        // never answers. Numbers only - == holds a text against TRUE as well, and a {pad:when
        // true} behind a {pad:when 'abc'} does answer for 'x'.

        foreach ( padParseOptions ( $padCaseText ) as $padCaseOne ) {

          if ( padMetaLiteral ( $padCaseOne ) === NULL )
            continue;

          $padCaseVal = padEval ( $padCaseOne );

          foreach ( $padCaseSeen as $padCaseOld )
            if ( $padCaseOld === $padCaseVal
                 or ( is_numeric ( $padCaseOld ) and is_numeric ( $padCaseVal ) and $padCaseOld == $padCaseVal ) )
              padError ( "the {pad:when " . trim ( $padCaseOne ) . "} is written twice - the first one answers" );

          $padCaseSeen [] = $padCaseVal;

        }

      }

      $padCaseScan += 9;

    }

  }

  $padIf      = padBranchText ( $padContent, $padChk, 9, $padPos );

  if ( $padCheckSyntax and trim ( $padIf ) == '' )
    padError ( "a {pad:when} of this {pad:case} has no value" );

  padBranchNested ( $padIf, 'a {pad:when}', "{pad:when \$y}" );

  $padContent = substr   ( $padContent, $padPos+1 );
  $padChk     = strpos   ( $padContent, '{pad:when' );

  // Under a coverage recording the branch that answers is noted (lib/coverage.php):
  // 'when <values>', 'else', or 'none' when nothing matched and there is no {pad:else}.

  $padCaseArm = "when $padIf";

  while ($padChk !== FALSE) {

    if ( ! padCheckTag ( 'case', substr ( $padContent, 0, $padChk ), $padContent ) )

      $padChk = strpos ( $padContent , '{pad:when', $padChk+9 );

    elseif ( padCaseWhen ( $padBasis, $padIf ) ) {

      $padContent = substr ( $padContent, 0, $padChk );

      if ( $padCoverageRun )
        padCoverageArm ( $padCaseArm );

      return TRUE;

    } else {

      $padIf      = padBranchText ( $padContent, $padChk, 9, $padPos );
      $padCaseArm = "when $padIf";

      if ( $padCheckSyntax and trim ( $padIf ) == '' )
        padError ( "a {pad:when} of this {pad:case} has no value" );

      padBranchNested ( $padIf, 'a {pad:when}', "{pad:when \$y}" );

      $padContent = substr   ( $padContent, $padPos+1 );
      $padChk     = strpos   ( $padContent, '{pad:when' );

    }

  }

  // An {pad:else} of our own ends the last branch and opens the default one, the same way
  // {pad:if} takes it: the part in front of it when that last {pad:when} matches, the part after it
  // when nothing has matched at all. padElseFree skips an {pad:else} belonging to a nested tag -
  // a {pad:case}, an {pad:if} or a condition tag, the other owners of an {pad:else}: a branch holding
  // {pad:if}...{pad:else}...{/pad:if} was split at the if's {pad:else}, and in the strict pre-scan above
  // every {pad:when} after it stood 'behind the {pad:else}'.

  $padChk = strpos ( $padContent, '{pad:else}' );

  while ( $padChk !== FALSE and ! padElseFree ( substr ( $padContent, 0, $padChk ), $padContent ) )
    $padChk = strpos ( $padContent, '{pad:else}', $padChk+10 );

  if ( $padChk !== FALSE ) {

    if ( padCaseWhen ( $padBasis, $padIf ) )
      $padContent = substr ( $padContent, 0, $padChk );
    else {
      $padContent = substr ( $padContent, $padChk+10 );
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
