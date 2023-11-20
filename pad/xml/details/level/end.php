<?php

  if ( $padResult [$pad] <> $padBase [$pad] )
    padFilePutContents ( "$padXmlDir/details/$padXmlLvl/pad-result.pad", $padResult [$pad] );

  padFilePutContents ( "$padXmlDir/details/$padXmlLvl/level-exit.json", $padXmlTree [$padXmlLvl] );

  if ( $pad > 0 ) {

    $padXmlStack = [];
    for ( $padI = $pad-1; $padI >= 0; $padI-- )
      $padXmlStack [] = $padXmlTree [ $padXmlLevel [$padI] ];

    padFilePutContents ( "$padXmlDir/details/$padXmlLvl/stack.json", $padXmlStack );

  }

?>