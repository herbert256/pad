<?php

  if ( ! $pqPull ) 
    return;
  
  foreach ( $padData [$pad] as $padK => $padV )
    if ( isset ( $pqStore [$pqPull] [$padK] ) )
      $padData [$pad] [$padK] [$pqPull] = $pqStore [$pqPull] [$padK];
 
?>