<?php

  if ( ! $pqOrgName ) 
    return;

  foreach ( $padData [$pad] as $padK => $padV )
    $padData [$pad] [$padK] [$pqOrgName] = $pqOrgHit [$padK];

?>