<?php

  if ( $padResult [$pad] <> $padBase [$pad] )
    padInfoPut ( "$padInfoDir/xml/details/$padXmlLvl/pad-result.pad", $padResult [$pad] );

  padInfoPut ( "$padInfoDir/xml/details/$padXmlLvl/level-exit.json", $padXmlTree [$padXmlLvl] );

  if ( $pad > 0 ) {

    $padXmlStack = [];
    for ( $padI = $pad-1; $padI >= 0; $padI-- )
      $padXmlStack [] = $padXmlTree [ $padXmlLevel [$padI] ];

    padInfoPut ( "$padInfoDir/xml/details/$padXmlLvl/stack.json", $padXmlStack );

  }

?>