<?php

  foreach ( $pqActionStart as $padK => $padV )
    if ( isset ( $pqActionEnd [$padK] ) )
      $pqResult [$padK] = $pqActionEnd [$padK];

  return $pqResult;

?>