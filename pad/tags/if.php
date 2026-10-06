<?php

  // The {if} tag together with its {elseif} chain: decides whether the level's content is
  // kept, and when there are several branches, which one of them survives.
  //
  // $padIf starts as the raw, unevaluated text of the first parameter, so padEval still sees
  // the comparison operators. The content is then scanned for {elseif, with padCheckTag
  // skipping the ones that belong to a nested {if}. On an {elseif} that really is ours the
  // condition collected so far is tested: if it holds, the content is cut off in front of
  // that {elseif} and TRUE returned; if not, the {elseif}'s own condition becomes $padIf,
  // everything up to and including it is dropped, and the scan continues. Returning FALSE
  // leaves the level to fall through to its @else@ half, which level/split.php separated out.

  // {if bool="name"} - the documented flag form: when what is written on the tag is the bool
  // option, the condition is that flag, resolved by options/bool.php against $padBoolStore.
  // The handler was in no phase list and nothing included it, so the form did nothing until
  // the audit - the raw option text fell through to padEval and answered TRUE for any name.

  // An {if} without its {/if} used to fall through as a single tag and render nothing of
  // what the author meant. Strict mode says what is missing.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  // The flag is settled here and stands in as a literal 1 or 0, so its {elseif} and {else}
  // are handled like those of any other condition - returning straight away left an
  // {else} in the content as a name nothing claims.

  if ( ( $padParms [$pad] [0] ['padPrmName'] ?? '' ) == 'bool' )
    $padIf = ( include PAD . 'options/bool.php' ) ? '1' : '0';
  else
    $padIf = $padParms [$pad] [0] ['padPrmOrg'] ?? '';

  if ( trim ( $padIf ) == '' and $padCheckSyntax )
    padError ( "the {if} has no condition" );

  // Under a coverage recording the branch that is taken is noted (lib/coverage.php): 'if',
  // 'elseif <condition>', 'else', or 'none' when nothing held and there is no {else}.

  $padIfArm = 'if';

  $padChk = strpos ($padContent, '{elseif');

  while ($padChk !== FALSE) {

    if ( ! padCheckTag ('if', substr($padContent, 0, $padChk)) )

      $padChk = strpos($padContent , '{elseif', $padChk+7);

    else {

      if ( padEval ($padIf ) )  {
        $padContent = substr ($padContent, 0, $padChk);
        if ( $padCoverageRun )
          padCoverageArm ( $padIfArm );
        return TRUE;
      }

      $padPos     = strpos($padContent, '}', $padChk);
      $padIf      = substr($padContent, $padChk+8, $padPos-($padChk+8));
      $padIfArm   = "elseif $padIf";

      if ( trim ( $padIf ) == '' and $padCheckSyntax )
        padError ( "an {elseif} of this {if} has no condition" );

      $padContent = substr($padContent, $padPos+1);
      $padChk     = strpos($padContent, '{elseif');

    }

  }

  // An {else} of our own splits what is left the way @else@ does: the part in front of it when
  // the condition holds, the part after it when it does not. padCheckTag skips an {else} that
  // belongs to a nested {if}, exactly as the {elseif} scan above does - and one that belongs
  // to a nested {case}, the other owner of an {else}: {if}{case}...{else}...{/case}{/if} split
  // at the case's {else}.
  //
  // Without this the tag was never implemented - there is no tags/else.php - so {else} was
  // left in the page as a name nothing claimed and both branches rendered.

  $padChk = strpos ( $padContent, '{else}' );

  while ( $padChk !== FALSE and ! ( padCheckTag ( 'if',   substr ( $padContent, 0, $padChk ) )
                                and padCheckTag ( 'case', substr ( $padContent, 0, $padChk ) ) ) )
    $padChk = strpos ( $padContent, '{else}', $padChk+6 );

  if ( $padChk !== FALSE ) {

    if ( padEvalBool ( $padIf ) )
      $padContent = substr ( $padContent, 0, $padChk );
    else {
      $padContent = substr ( $padContent, $padChk+6 );
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
