<?php
  
  $padPagePad [$pad] = $padPageApp [$pad] = [];

  foreach ($GLOBALS as $padK => $padV ) {

    if ( substr($padK, 0, 3) == 'pad' and ! in_array($padK, $padLevelVars) and $padK <> 'padK' and $padK <> 'padV' ) 
      $padPagePad [$pad] [$padK] = $GLOBALS [$padK];
 
    if ( padValidStore ($padK) ) {
      $padPageApp [$pad] [$padK] = $GLOBALS [$padK];
      unset ( $GLOBALS [$padK] );
    }

  }

  foreach ( $padSetLvl [$pad] as $padK => $padV ) {

    if ( array_key_exists ($padK,  $GLOBALS) )
      unset ( $GLOBALS [$padK] );

    $GLOBALS [$padK] = $padV;

  }

  $padTables = $padRelations = $padDataStore = $padContentStore = $padFlagStore = $padSeqStore = [];
   
  if ( isset ( $padSqlConnect ) )
    unset ( $padSqlConnect );

?>