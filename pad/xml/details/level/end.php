<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXml [$padXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  if ( $padResult [$pad] <> $padBase [$pad] )
    padFilePutContents ( "$padXmlDir/levels/$padXmlLvl/pad-result.pad", $padResult [$pad] );

  padFilePutContents ( "$padXmlDir/levels/$padXmlLvl/level-exit.json", $padXml [$padXmlLvl] );

  if ( $pad > 0 ) {

    $padXmlStack = [];
    for ( $padI = $pad-1; $padI >= 0; $padI-- )
      $padXmlStack [] = $padXml [ $padXmlLevel [$padI] ];

    padFilePutContents ( "$padXmlDir/levels/$padXmlLvl/stack.json", $padXmlStack );

  }

?>