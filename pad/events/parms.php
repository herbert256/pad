<?php

  if (  $GLOBALS ['padInfoXref'] )
    foreach ( $padPrm [$pad] as $padK => $padV )
      padInfoXref ( "_options/$padK", $padType [$pad], $padTag [$pad] );

  if ( $GLOBALS ['padInfoXml']   )
    include PAD . 'info/types/xml/level/parms.php';

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
