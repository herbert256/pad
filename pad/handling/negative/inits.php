<?php

  $padHandOld     = $padData [$pad];
  $padData [$pad] = [];

  foreach ( $padHandOld as $padK => $padV )
    $padData [$pad] [ 'x' . $padK ] = $padV;

  $padHandOld = $padData [$pad];

?>
