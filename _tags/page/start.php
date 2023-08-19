<?php
  
  $padPagePad [$pad] = $padPageApp [$pad] = $padPageSave [$pad] = $padPageDelete [$pad] = [];

  foreach ($GLOBALS as $padK => $padV ) {

    if ( substr($padK, 0, 3) == 'pad' and ! in_array($padK, $padLevelVars) and $padK <> 'padK' and $padK <> 'padV' ) 
      if ( $padK <> 'padHistory' )
        $padPagePad [$pad] [$padK] = $GLOBALS [$padK];
 
    if ( padValidStore ($padK) ) {
      $padPageApp [$pad] [$padK] = $GLOBALS [$padK];
      unset ( $GLOBALS [$padK] );
    }

  }

  $padTables = $padRelations = $padDataStore = $padContentStore = $padFlagStore = $padSeqStore = [];

  foreach ( $padSetLvl [$pad] as $padK => $padV ) {

    if ( array_key_exists ($padK,  $GLOBALS) )
      unset ( $GLOBALS [$padK] );

    $GLOBALS [$padK] = $padV;

  }
   
  if ( isset ( $padSqlConnect ) )
    unset ( $padSqlConnect );

?>