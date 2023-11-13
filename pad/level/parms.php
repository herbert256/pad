<?php

  $padOptCnt = 0;

  $padPrmParse = padParseOptions ( $padOpt [$pad] [0] );

  foreach ( $padPrmParse as $padK => $padV ) {

    list ($padPrmName, $padPrmValue ) = padSplit ('=', trim($padV));

    if ( substr($padPrmName, 0, 1) == '$' ) {

      $padSetName = substr($padPrmName, 1);

      if ( padValidVar ($padSetName) and $padPrmValue !== '' ) {
        $padSetLvl [$pad] [$padSetName] = padVarOpts ( '', padExplode($padPrmValue, '|') );
        continue;
      }
  
    }

    if ( substr($padPrmName, 0, 1) == '%' ) {

      $padSetName = substr($padPrmName, 1);

      if ( padValidVar ($padSetName) ) {
        $padSetOcc [$pad] [$padSetName] = $padPrmValue;
        continue;
      }
  
    }

    if ( padValidVar ($padPrmName) ) {

      if ( padExists ( padApp . "options/$padPrmName.php") )
        $padOptionsAppStart [$pad] [] = $padPrmName;

      $padPrm [$pad] [$padPrmName] = ( $padPrmValue === '' ) ? TRUE : padEval ( $padPrmValue );

      continue;

    }

    $padOptCnt++;
    $padOpt [$pad] [$padOptCnt] = padEval ( $padV );

  }

  if ( $padTraceActive )
    include pad . 'trace/items/parms.php';

?>