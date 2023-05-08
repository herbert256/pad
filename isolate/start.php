<?php

  $padIsolate [$pad] = [];
      
  foreach ($GLOBALS as $padK => $padV)
    if ( padValidStore ($padK) )
      $padIsolate [$pad] [$padK] = $GLOBALS [$padK];

?>