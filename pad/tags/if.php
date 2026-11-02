<?php

  // The {pad:if} tag together with its {pad:elseif} chain: decides whether the level's content is
  // kept, and when there are several branches, which one of them survives.
  //
  // $padIf starts as the raw, unevaluated text of the first parameter, so padEval still sees
  // the comparison operators. The content is then scanned for {pad:elseif, with padCheckTag
  // skipping the ones that belong to a nested {pad:if}. On an {pad:elseif} that really is ours the
  // condition collected so far is tested: if it holds, the content is cut off in front of
  // that {pad:elseif} and TRUE returned; if not, the {pad:elseif}'s own condition becomes $padIf,
  // everything up to and including it is dropped, and the scan continues. Returning FALSE
  // leaves the level to fall through to its @else@ half, which level/split.php separated out.

  // {pad:if bool="name"} - the documented flag form: when what is written on the tag is the bool
  // option, the condition is that flag, resolved by options/bool.php against $padBoolStore.
  // The handler was in no phase list and nothing included it, so the form did nothing until
  // the audit - the raw option text fell through to padEval and answered TRUE for any name.

  // An {pad:if} without its {/pad:if} used to fall through as a single tag and render nothing of
  // what the author meant. Strict mode says what is missing.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  // The flag is settled here and stands in as a literal 1 or 0, so its {pad:elseif} and {pad:else}
  // are handled like those of any other condition - returning straight away left an
  // {pad:else} in the content as a name nothing claims.

  if ( ( $padParms [$pad] [0] ['padPrmName'] ?? '' ) == 'bool' )
    $padIf = ( include PAD . 'options/bool.php' ) ? '1' : '0';
  else
    $padIf = padFirstParm ();

  if ( trim ( $padIf ) == '' and $padCheckSyntax )
    padError ( "the {pad:if} has no condition" );

  // Under a coverage recording the branch that is taken is noted (lib/coverage.php): 'if',
  // 'elseif <condition>', 'else', or 'none' when nothing held and there is no {pad:else}.

  $padIfArm = 'if';

  // The branches stand in order: the {pad:elseif}s, then one {pad:else}. An {pad:elseif} behind the
  // {pad:else} never answers - the branch in front of it was lost in silence, or with another
  // value the kept part ended in an {pad:else} nothing claimed, "there is no tag named 'else'";
  // a second {pad:else} the same. Strict mode names it, as case.php does for a {pad:when} behind
  // its {pad:else}.

  if ( $padCheckSyntax ) {

    $padIfElse = strpos ( $padContent, '{pad:else}' );

    while ( $padIfElse !== FALSE and ! padElseFree ( substr ( $padContent, 0, $padIfElse ), $padContent ) )
      $padIfElse = strpos ( $padContent, '{pad:else}', $padIfElse+10 );

    if ( $padIfElse !== FALSE ) {

      for ( $padIfAt = strpos ( $padContent, '{pad:elseif', $padIfElse ); $padIfAt !== FALSE; $padIfAt = strpos ( $padContent, '{pad:elseif', $padIfAt+11 ) )
        if ( padCheckTag ( 'if', substr ( $padContent, 0, $padIfAt ), $padContent ) )
          padError ( "an {pad:elseif} behind the {pad:else} never answers" );

      for ( $padIfAt = strpos ( $padContent, '{pad:else}', $padIfElse+10 ); $padIfAt !== FALSE; $padIfAt = strpos ( $padContent, '{pad:else}', $padIfAt+10 ) )
        if ( padElseFree ( substr ( $padContent, 0, $padIfAt ), $padContent ) )
          padError ( "the {pad:if} has two {pad:else}s - what follows the second never renders" );

    }

  }

  $padChk = strpos ($padContent, '{pad:elseif');

  while ($padChk !== FALSE) {

    if ( ! padCheckTag ( 'if', substr ( $padContent, 0, $padChk ), $padContent ) )

      $padChk = strpos($padContent , '{pad:elseif', $padChk+11);

    else {

      if ( padEval ($padIf ) )  {
        $padContent = substr ($padContent, 0, $padChk);
        if ( $padCoverageRun )
          padCoverageArm ( $padIfArm );
        return TRUE;
      }

      $padIf      = padBranchText ( $padContent, $padChk, 11, $padPos );
      $padIfArm   = "elseif $padIf";

      if ( trim ( $padIf ) == '' and $padCheckSyntax )
        padError ( "an {pad:elseif} of this {pad:if} has no condition" );

      padBranchNested ( $padIf, 'an {pad:elseif}', "{pad:elseif \$x eq 'b'}" );

      $padContent = substr($padContent, $padPos+1);
      $padChk     = strpos($padContent, '{pad:elseif');

    }

  }

  // An {pad:else} of our own splits what is left the way @else@ does: the part in front of it when
  // the condition holds, the part after it when it does not. padCheckTag skips an {pad:else} that
  // belongs to a nested {pad:if}, exactly as the {pad:elseif} scan above does - and one that belongs
  // to a nested {pad:case} or condition tag, the other owners of an {pad:else} (padElseFree):
  // {pad:if}{pad:case}...{pad:else}...{/pad:case}{/pad:if} split at the case's {pad:else}.
  //
  // Without this the tag was never implemented - there is no tags/else.php - so {pad:else} was
  // left in the page as a name nothing claimed and both branches rendered.

  $padChk = strpos ( $padContent, '{pad:else}' );

  while ( $padChk !== FALSE and ! padElseFree ( substr ( $padContent, 0, $padChk ), $padContent ) )
    $padChk = strpos ( $padContent, '{pad:else}', $padChk+10 );

  if ( $padChk !== FALSE ) {

    if ( padEvalBool ( $padIf ) )
      $padContent = substr ( $padContent, 0, $padChk );
    else {
      $padContent = substr ( $padContent, $padChk+10 );
      $padIfArm   = 'else';
    }

    if ( $padCoverageRun )
      padCoverageArm ( $padIfArm );

    return TRUE;

  }

  $padIfHeld = padEvalBool ( $padIf );

  if ( $padCoverageRun )
    padCoverageArm ( $padIfHeld ? $padIfArm : 'none' );

  return $padIfHeld;

?>
