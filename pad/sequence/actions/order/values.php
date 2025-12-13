<?php

  foreach ( $pqActionStart as $padV ) {
    $padK = array_search ( $padV, $pqActionEnd );
    if ( $padK !== FALSE )
      $pqResult [$padK] = $pqActionEnd [$padK];
  }
  
  return $pqResult;

?>