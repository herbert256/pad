<?php

  // Stores a variable assignment written as a tag parameter.
  //
  // {tag $x = expr} is a level variable: evaluated once, here, into $padSetLvl [$pad], so
  // it stays constant for the whole tag. {tag %x = expr} is an occurrence variable: the
  // expression itself is kept in $padSetOcc [$pad] and occurrence/set.php re-evaluates it
  // for every row.

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
    $padParmsSetValue = $padSetOcc [$pad] [$padSetName];

  }

?>