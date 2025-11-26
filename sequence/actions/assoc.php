<?php

  $pqActionStart = $pqResult;
  $pqResult      = [];

  foreach ( $pqActionStart as $padK => $padV )
    $pqResult [ 'x' . $padK ] = $padV;

?>