<?php

  // Negative selection by value: keeps the entries of the pre-action snapshot
  // $pqActionStart whose value does not appear in the action's result $pqActionEnd. Used
  // when the action rebuilt the array, so its keys can no longer be matched.

  foreach ( $pqActionStart as $padK => $padV )
    if ( ! in_array ($padV, $pqActionEnd ) )
      $pqResult [$padK] = $padV;

?>