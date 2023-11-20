<?php

  padFilePutContents ( "$padXmlDir/details/$padXmlLvl/pad-base.pad", $padBase [$pad] );

  if ( ! padIsDefaultData ( $padData [$pad] ) )
    padFilePutContents ( "$padXmlDir/details/$padXmlLvl/data.json", $padData [$pad] );

  $padXmlInfo = [
    'xml'     => $padXmlTree [$padXmlLvl],
    'name'    => $padName [$pad],
    'null'    => $padNull [$pad],
    'else'    => $padElse [$pad],
    'hit'     => $padHit [$pad],
    'array'   => $padArray [$pad],
    'text'    => $padText  [$pad],
    'default' => $padDefault [$pad],
    'count'   => $padCount [$pad]
  ];

  padFilePutContents ( "$padXmlDir/details/$padXmlLvl/level-info.json", $padXmlInfo );

?>