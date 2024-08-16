<?php

  if ( $GLOBALS ['padInfoTrace'] and $padInfoTraceParms ) {

    foreach ( $padOpt [$pad] as $padK => $padV )
      if ( $padK and $padV )
       if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'parm', 'opt',  "$padK ==> $padV" );

    foreach ( $padPrm [$pad] as $padK => $padV )
     if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'parm', 'prm',  "$padK ==> $padV" );
    
    foreach ( $padSetLvl [$pad] as $padK => $padV )
     if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'parm', 'lvl',  "$padK ==> $padV" );
    
    foreach ( $padSetOcc [$pad] as $padK => $padV )
     if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'parm', 'occ',  "$padK ==> $padV" );

  }

  if ( $GLOBALS ['padInfoXml']   ) include '/pad/info/xml/level/parms.php';  
  if ( $GLOBALS ['padInfoTrace'] ) include '/pad/info/events/parms.php';

  foreach ( $padPrm [$pad] as $padK => $padV )
   if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'options', $padK );

  if ( $padType [$pad] == 'pad' ) {

    foreach ( $padPrm [$pad] as $padK => $padV )
     if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'tags-options', $padTag[$pad], $padK );

    foreach ( $padPrm [$pad] as $padK => $padV )
     if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'options-tags', $padK, $padTag[$pad] );

  }
    

?>