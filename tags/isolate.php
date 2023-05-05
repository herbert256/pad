<?php

  if ( $padWalk [$pad] == 'start' ) {

    $padWalk [$pad] = 'end';
    
    $padIsolate [$pad] = [];
      
    foreach ($GLOBALS as $padK => $padV)
      if ( padValidStore ($padK) )
        $padIsolate [$pad] [$padK] = $GLOBALS [$padK];
    
  } else {

    foreach ($GLOBALS as $padK => $padV)
      if ( padValidStore ($padK) and ! in_array($padK, $padIsolate [$pad]) )
        unset ( $GLOBALS[$padK] );

    foreach ( $padIsolate [$pad] as $padK => $padV )
      $GLOBALS [$padK] = $padIsolate [$pad] [$padK];
  
  }

  return TRUE;

?>