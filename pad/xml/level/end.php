<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXml [$padXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  if ( $padResult [$pad] <> $padBase [$pad] )
    padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/pad-result.pad", $padResult [$pad] );

  padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/level-exit.json", $padXml [$padXmlLvl] );

  if ( $pad > 0 ) {

    $padXmlStack = [];
    for ( $padI = $pad-1; $padI >= 0; $padI-- )
      $padXmlStack [] = $padXml [ $padXmlLevel [$padI] ];

    padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/stack.json", $padXmlStack );

  }

  $padXmlEventType = 'level-end';
  include pad . 'xml/event.php';

?>