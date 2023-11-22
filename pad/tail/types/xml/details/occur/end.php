<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];
 
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  if ( ! $padDefault [$pad] )
    padTailPut ( "$padXmlDir/$padTailId/details/$padXmlLvl/$padXmlOcc/result.pad", $padPad [$pad] );

?>