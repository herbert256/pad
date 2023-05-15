<?php

  $padTable [$pad] = [];

  if ( $padTableTag [$pad] )
    $padTable [$pad] [$padTableTag[$pad]] = $padCurrent [$pad];

  foreach ($padRelations as $padK => $padV)
    foreach ($padRelations[$padK] as $padK2 => $padV2)
      if ( ! isset($padTables [$padK2] ) ) {
        $padTables [$padK2] = $padTables [$padRelations[$padK] [$padK2] ['table']];
        $padTables [$padK2] ['virtual'] = TRUE;
      }    

  while ( padTableGetInfo () ) ;
  
  foreach ( $padTable [$pad] as $padK => $padV)
    foreach ( $padV as $padK2 => $padV2)
      if ( ! isset($GLOBALS [$padK2] ) )
        padSetGlobal ( $padK2, $padV2 );

  foreach ( $padTable [$pad] as $padK => $padV)
    if ( ! isset($GLOBALS [$padK] ) )
      padSetGlobal ( $padK, $padV );

?>