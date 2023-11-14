<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];
 
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  if ( ! $padDefault [$pad] )
    padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/$padXmlOcc/pad-end.pad", $padPad [$pad] );

  $padXmlEventType = 'occur-end';
  include pad . 'xml/event.php';

?>