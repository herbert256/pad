<?php

  // Order restore by value: walks the pre-action snapshot $pqActionStart and re-emits
  // each value the action kept, in the snapshot's order. Used when the keys can no
  // longer be matched; repeated values all resolve to the first entry holding them.
  // Returns $pqResult to the include.

  foreach ( $pqActionStart as $padV ) {
    $padK = array_search ( $padV, $pqActionEnd );
    if ( $padK !== FALSE )
      $pqResult [$padK] = $pqActionEnd [$padK];
  }

  return $pqResult;

?>