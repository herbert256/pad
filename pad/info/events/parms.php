<?php

  if ( $GLOBALS ['padInfo'] and $padInfTraceParms ) {

    foreach ( $padOpt [$pad] as $padK => $padV )
      if ( $padK and $padV )
       padTrace ( 'parm', 'opt',  "$padK ==> $padV" );

    foreach ( $padPrm [$pad] as $padK => $padV )
     padTrace ( 'parm', 'prm',  "$padK ==> $padV" );
    
    foreach ( $padSetLvl [$pad] as $padK => $padV )
     padTrace ( 'parm', 'lvl',  "$padK ==> $padV" );
    
    foreach ( $padSetOcc [$pad] as $padK => $padV )
     padTrace ( 'parm', 'occ',  "$padK ==> $padV" );

  }

  if ( padXml  ) include '/pad/info/types/xml/level/parms.php';  
  if ( $GLOBALS ['padInfo'] ) include '/pad/info/events/parms.php';

  foreach ( $padPrm [$pad] as $padK => $padV )
   padTrace ( 'options', $padK );

  if ( $padType [$pad] == 'pad' ) {

    foreach ( $padPrm [$pad] as $padK => $padV )
     padTrace ( 'tags-options', $padTag[$pad], $padK );

    foreach ( $padPrm [$pad] as $padK => $padV )
     padTrace ( 'options-tags', $padK, $padTag[$pad] );

  }
    

?>