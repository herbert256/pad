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

  $padIf  = $padParms [$pad] [0] ['padPrmOrg'];
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
      $padContent = substr($padContent, $padPos+1);
      $padChk     = strpos($padContent, '{elseif');

    }

  }

  return padEvalBool ( $padIf );

?>