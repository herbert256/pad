<?php

  foreach ( $padData [$pad] as $padK => $padV )
    if ( isset ( $pqOrgHit [$padK] ) )
      $padData [$pad] [$padK] [$pqOrgName] = $pqOrgHit [$padK];
    else
      $padData [$pad] [$padK] [$pqOrgName] = 'n/a';

?>
