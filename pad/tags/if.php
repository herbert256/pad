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
    return padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  if ( ( $padParms [$pad] [0] ['padPrmName'] ?? '' ) == 'bool' )
    return ( include PAD . 'options/bool.php' ) ? TRUE : FALSE;

  $padIf  = $padParms [$pad] [0] ['padPrmOrg'] ?? '';

  if ( trim ( $padIf ) == '' and $padCheckSyntax )
    return padError ( "the {if} has no condition" );

  $padChk = strpos ($padContent, '{elseif');

  while ($padChk !== FALSE) {

    if ( ! padCheckTag ('if', substr($padContent, 0, $padChk)) )

      $padChk = strpos($padContent , '{elseif', $padChk+7);

    else {

      if ( padEval ($padIf ) )  {
        $padContent = substr ($padContent, 0, $padChk);
        return TRUE;
      }

      $padPos     = strpos($padContent, '}', $padChk);
      $padIf      = substr($padContent, $padChk+8, $padPos-($padChk+8));

      if ( trim ( $padIf ) == '' and $padCheckSyntax )
        return padError ( "an {elseif} of this {if} has no condition" );

      $padContent = substr($padContent, $padPos+1);
      $padChk     = strpos($padContent, '{elseif');

    }

  }

  // An {else} of our own splits what is left the way @else@ does: the part in front of it when
  // the condition holds, the part after it when it does not. padCheckTag skips an {else} that
  // belongs to a nested {if}, exactly as the {elseif} scan above does.
  //
  // Without this the tag was never implemented - there is no tags/else.php - so {else} was
  // left in the page as a name nothing claimed and both branches rendered.

  $padChk = strpos ( $padContent, '{else}' );

  while ( $padChk !== FALSE and ! padCheckTag ( 'if', substr ( $padContent, 0, $padChk ) ) )
    $padChk = strpos ( $padContent, '{else}', $padChk+6 );

  if ( $padChk !== FALSE ) {

    if ( padEvalBool ( $padIf ) )
      $padContent = substr ( $padContent, 0, $padChk );
    else
      $padContent = substr ( $padContent, $padChk+6 );

    return TRUE;

  }

  return padEvalBool ( $padIf );

?>