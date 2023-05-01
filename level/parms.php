<?php

  $padPrmParse = padParseOptions ( $padOpt [$pad] [0] );

  foreach ( $padPrmParse as $padK => $padV ) {

    list ($padPrmName, $padPrmValue ) = padSplit ('=', $padV);

    if ( substr($padPrmName, 0, 1) == '$' ) {

      $padSetName = substr($padPrmName, 1);

      if ( padValidVar ($padSetName) and $padPrmValue !== '' ) {
        $padSet [$pad] [$padSetName] = padVarOpts ( '', padExplode($padPrmValue, '|') );
        continue;
      }
  
    }

    if ( padValidVar ($padPrmName) ) {

      if ( padExists ( padApp . "options/$padPrmName.php") )
        $padOptionsApp [$pad] [] = $padPrmName;

      $padPrm [$pad] [$padPrmName] = ( $padPrmValue === '' ) ? TRUE : padEval ( $padPrmValue );

      continue;

    }

    $padOpt [$pad] [$padK+1] = padEval ( $padV );

  }

?>