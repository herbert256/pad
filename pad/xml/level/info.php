<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];

  padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/pad-base.pad", $padBase [$pad] );

  if ( ! padIsDefaultData ( $padData [$pad] ) )
    padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/data.json", $padData [$pad] );

  $padXml [$padXmlLvl] ['result'] = $padXmlTagReturn;
  $padXml [$padXmlLvl] ['source'] = include pad . 'xml/level/status.php';

  $padXmlInfo = [
    'xml'     => $padXml [$padXmlLvl],
    'name'    => $padName [$pad],
    'null'    => $padNull [$pad],
    'else'    => $padElse [$pad],
    'hit'     => $padHit [$pad],
    'array'   => $padArray [$pad],
    'text'    => $padText  [$pad],
    'default' => $padDefault [$pad],
    'count'   => $padCount [$pad]
  ];

  padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/level-info.json", $padXmlInfo );

?>