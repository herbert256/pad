<?php

  if ( $GLOBALS ['padInfoXref'] or $GLOBALS ['padInfoXapp'] ) 
    foreach ( $padPrm [$pad] as $padK => $padV )
      padInfoXapp ( 'options', $padK );

  if ( $GLOBALS ['padInfoXref'] ) { 

    foreach ( $padPrm [$pad] as $padK => $padV )
      padInfoXref ( 'options/tags-options', $padTag [$pad], $padK );

    foreach ( $padPrm [$pad] as $padK => $padV )
      padInfoXref ( 'options/options-tags', $padK, $padTag [$pad] );

    foreach ( $padPrm [$pad] as $padK => $padV )
      padInfoXref ( 'options/types-options', $padType [$pad], $padK );

    foreach ( $padPrm [$pad] as $padK => $padV )
      padInfoXref ( 'options/options-types', $padK, $padType [$pad] );

  }

  if ( $GLOBALS ['padInfoXml']   ) 
    include '/pad/info/xml/level/parms.php';  

  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceParms'] ) {

    foreach ( $padOpt [$pad] as $padK => $padV )
      if ( $padK and $padV )
        padInfoTrace ( 'parm', 'opt',  "$padK ==> $padV" );

    foreach ( $padPrm [$pad] as $padK => $padV )
      padInfoTrace ( 'parm', 'prm',  "$padK ==> $padV" );
    
    foreach ( $padSetLvl [$pad] as $padK => $padV )
      padInfoTrace ( 'parm', 'lvl',  "$padK ==> $padV" );
    
    foreach ( $padSetOcc [$pad] as $padK => $padV )
      padInfoTrace ( 'parm', 'occ',  "$padK ==> $padV" );

  }

?>