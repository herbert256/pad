<?php

  foreach ( $pqActionStart as $padK => $padV )
    if ( ! in_array ($padV, $pqActionEnd ) )
      $pqResult [$padK] = $padV;

?>
