<?php

  if ( ! $pqPull                                                 ) return;
  if ( count ( $pqStore [$pqPull] ) <> count ( $padData [$pad] ) ) return;

  $padData [$pad] = array_values ( $padData [$pad] );

  foreach ( $padData [$pad] as $padK => $padV )
    $padData [$pad] [$padK] [$pqPull] = $pqStore [$pqPull] [$padK];
 
?>