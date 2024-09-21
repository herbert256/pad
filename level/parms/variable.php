<?php

  $padSetName = substr ( $padPrmName, 1 );

  if ( substr ( $padPrmName, 0, 1 ) == '$' ) {

    $padSetLvl [$pad] [$padSetName] = padVarOpts ( '', padExplode($padPrmValue, '|') );
    $padParmParse [$pad] [$padSetName] = 'lvl';

  } else {
 
    $padSetOcc [$pad] [$padSetName] = $padPrmValue;
    $padParmParse [$pad] [$padSetName] = 'occ';

  }

?>