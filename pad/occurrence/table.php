<?php

  $padTable [$pad] = [];

  if ( isset ($padTableTag [$pad]) and $padTableTag [$pad] )
    $padTable [$pad] [$padTableTag[$pad]] = $padCurrent [$pad];

  foreach ($padRelations as $padK => $padV)
    foreach ($padRelations[$padK] as $padK2 => $padV2)
      if ( ! isset($padTables [$padK2] ) ) {
        $padTables [$padK2] = $padTables [$padRelations[$padK] [$padK2] ['table']];
        $padTables [$padK2] ['virtual'] = TRUE;
      }    

  while ( padTableGetInfo () ) ;

?>