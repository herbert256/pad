<?php

  if ( ! $pqBuildName or $pqBuildName == TRUE )
    return;

  if ( pqStore ( $pqBuild ) ) {

    foreach ( $pqPlays as $padK => $padV ) {
      $pqPlays [$padK] ['pqBuild'] = $pqBuildName;
      return;
    }

  } else

    $pqBuild = $pqBuildName;

?>
