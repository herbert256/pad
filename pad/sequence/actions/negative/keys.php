<?php

  foreach ( $pqActionStart as $padK => $padV )
    if ( ! isset ( $pqActionEnd [$padK] ) )
      $pqResult [$padK] = $padV;

?>
