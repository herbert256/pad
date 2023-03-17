<?php

  $padTable [$pad] = [];

  if ( $padTableTag [$pad] )
    $padTable [$pad] [$padTableTag[$pad]] = $padCurrent [$pad];

  foreach ($padRelations as $key => $val)
    foreach ($padRelations[$key] as $key2 => $val2)
      if ( ! isset($padTables [$key2] ) ) {
        $padTables [$key2] = $padTables [$padRelations[$key] [$key2] ['table']];
        $padTables [$key2] ['virtual'] = TRUE;
      }    

  while ( padTableGetInfo () ) ;
  
  foreach ( $padTable [$pad] as $padK => $padV)
    if (  ! isset($GLOBALS [$padK] ) )
      padSetGlobal ( $padK, $padV );

  foreach ( $padTable [$pad] as $padK => $padV)
    foreach ( $padV as $padK2 => $padV2)
      if (  ! isset($GLOBALS [$padK2] ) )
        padSetGlobal ( $padK2, $padV2 );

?>