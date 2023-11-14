<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXml [$padXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  if ( $padResult [$pad] <> $padBase [$pad] )
    padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/pad-result.pad", $padResult [$pad] );

  padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/level-exit.json", $padXml [$padXmlLvl] );

  $padXmlEventType = 'level-end';
  include pad . 'xml/event.php';

?>