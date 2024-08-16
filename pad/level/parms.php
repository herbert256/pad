<?php

  if ( $padPrmType [$pad] == 'close' and str_contains( $padOpt [$pad] [0], '}' ) ) 
    include '/pad/level/close.php';

  $padOptCnt = 0;

  foreach ( $padPrmParse as $padK => $padV ) {

    list ($padPrmName, $padPrmValue ) = padSplit ('=', trim($padV));

    if ( substr($padPrmName, 0, 1) == '$' ) {

      $padSetName = substr($padPrmName, 1);

      if ( padValidVar ($padSetName) and $padPrmValue !== '' ) {
        $padSetLvl [$pad] [$padSetName] = padVarOpts ( '', padExplode($padPrmValue, '|') );
        $padParmParse [$pad] [$padSetName] = 'lvl';
        continue;
      }
  
    }

    if ( substr($padPrmName, 0, 1) == '%' ) {

      $padSetName = substr($padPrmName, 1);

      if ( padValidVar ($padSetName) ) {
        $padSetOcc [$pad] [$padSetName] = $padPrmValue;
        $padParmParse [$pad] [$padSetName] = 'occ';
        continue;
      }
  
    }

    if ( padValidVar ($padPrmName) ) {

      if ( file_exists ( "/app/options/$padPrmName.php") )
        $padOptionsAppStart [$pad] [] = $padPrmName;

      $padPrm [$pad] [$padPrmName] = $padStartOptions [$padPrmName];

      continue;

    }

    $padOptCnt++;
    $padOpt [$pad] [$padOptCnt] = padEval ( $padV );

  }

  if ( $GLOBALS['padInfo'] ) 
    include '/pad/info/events/parms.php';

?>