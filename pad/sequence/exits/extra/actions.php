<?php

  if ( ! count ( $pqActionsHit ) )
    return;

  foreach ( $padData [$pad] as $padK => $padV )
    foreach ( $pqActionsHit as $pqAction => $pqActionResult )
      if ( isset ( $pqActionResult [$padK] ) )
        $padData [$pad] [$padK] [$pqAction] = $pqActionResult [$padK];
      else
        $padData [$pad] [$padK] [$pqAction] = 'n/a';

?>
