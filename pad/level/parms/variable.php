<?php

  $padSetName = substr ( $padPrmName, 1 );

  if ( substr ( $padPrmName, 0, 1 ) == '$' ) {

    $padSetLvl [$pad] [$padSetName] = padEval ( $padPrmValue );
    $padParmParse [$pad] [$padSetName] = 'lvl';

    $padParmsSet = $padSetLvl [$pad] [$padSetName];

    $padParmsSetType  = 'lvl';
    $padParmsSetName  = $padSetName;
    $padParmsSetValue = $padSetLvl [$pad] [$padSetName];

  } else {
 
    $padSetOcc [$pad] [$padSetName] = $padPrmValue;
    $padParmParse [$pad] [$padSetName] = 'occ';

    $padParmsSet = $padSetOcc [$pad] [$padSetName];

    $padParmsSetType  = 'occ';
    $padParmsSetName  = $padSetName;
    $padParmsSetValue = 'todo';

  }

?>