<?php

  // Negative selection by key: keeps the entries of the pre-action snapshot
  // $pqActionStart whose key is absent from the action's result $pqActionEnd. Used when
  // the action preserved keys.

  foreach ( $pqActionStart as $padK => $padV )
    if ( ! isset ( $pqActionEnd [$padK] ) )
      $pqResult [$padK] = $padV;

?>