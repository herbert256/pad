<?php

  // Order restore by key: walks the pre-action snapshot $pqActionStart and re-emits the
  // entries the action kept, in the snapshot's order. Returns $pqResult to the include.

  foreach ( $pqActionStart as $padK => $padV )
    if ( isset ( $pqActionEnd [$padK] ) )
      $pqResult [$padK] = $pqActionEnd [$padK];

  return $pqResult;

?>