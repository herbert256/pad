<?php

  if ( $padResult [$pad] <> $padBase [$pad] )
    padTailPut ( "$padXmlDir/$padTailId/details/$padXmlLvl/pad-result.pad", $padResult [$pad] );

  padTailPut ( "$padXmlDir/$padTailId/details/$padXmlLvl/level-exit.json", $padXmlTree [$padXmlLvl] );

  if ( $pad > 0 ) {

    $padXmlStack = [];
    for ( $padI = $pad-1; $padI >= 0; $padI-- )
      $padXmlStack [] = $padXmlTree [ $padXmlLevel [$padI] ];

    padTailPut ( "$padXmlDir/$padTailId/details/$padXmlLvl/stack.json", $padXmlStack );

  }

?>