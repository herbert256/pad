<?php

  $padTreeLvl = $padTreeLevel [$pad];
  $padTreeOcc = $padOccur     [$pad];

  padFilePutContents ( "$padTreeDir/$padTreeLvl/pad-base.pad", $padBase [$pad] );

  if ( ! padIsDefaultData ( $padData [$pad] ) )
    padFilePutContents ( "$padTreeDir/$padTreeLvl/data.json", $padData [$pad] );

  $padTree [$padTreeLvl] ['result'] = $padTreeTagReturn;
  $padTree [$padTreeLvl] ['source'] = include pad . 'tree/level/status.php';

  $padTreeInfo = [
    'xml'     => $padTree [$padTreeLvl],
    'name'    => $padName [$pad],
    'null'    => $padNull [$pad],
    'else'    => $padElse [$pad],
    'hit'     => $padHit [$pad],
    'array'   => $padArray [$pad],
    'text'    => $padText  [$pad],
    'default' => $padDefault [$pad],
    'count'   => $padCount [$pad]
  ];

  padFilePutContents ( "$padTreeDir/$padTreeLvl/level-info.json", $padTreeInfo );

?>