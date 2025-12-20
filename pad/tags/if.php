<?php

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