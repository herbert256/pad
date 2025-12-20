<?php

  $padCeaseLevel = padFindContinueBreak ( $padParm );

  $padCease = FALSE;

  foreach ( $padData [$padCeaseLevel] as $padK => $padV )

    if ( $padK == $padKey [$pad] )

      $padCease = TRUE;

    elseif ( $padCease )

      unset (  $padData [$padCeaseLevel] [$padK] );

  return TRUE;

?>
