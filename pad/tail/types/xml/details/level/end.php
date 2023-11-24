<?php

  if ( $padResult [$pad] <> $padBase [$pad] )
    padTailPut ( "$padTailDir/xml/details/$padXmlLvl/pad-result.pad", $padResult [$pad] );

  padTailPut ( "$padTailDir/xml/details/$padXmlLvl/level-exit.json", $padXmlTree [$padXmlLvl] );

  if ( $pad > 0 ) {

    $padXmlStack = [];
    for ( $padI = $pad-1; $padI >= 0; $padI-- )
      $padXmlStack [] = $padXmlTree [ $padXmlLevel [$padI] ];

    padTailPut ( "$padTailDir/xml/details/$padXmlLvl/stack.json", $padXmlStack );

  }

?>