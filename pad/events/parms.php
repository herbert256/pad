<?php

  // Fires from level/parms/parms.php once a tag's parameters have all been parsed into
  // $padOpt, $padPrm and the level/occurrence variable sets.
  //
  // For the xref it records which tag types use which option; for the xml report it adds the
  // level's parameter block; for the trace it dumps every parameter, option and $-/%-variable
  // assignment when $padInfoTraceParms is on.

  global $padInfoTrace, $padInfoTraceParms, $padInfoXml, $padInfoXref;

  if (  $padInfoXref )
    foreach ( $padPrm [$pad] as $padK => $padV )
      padInfoXref ( "_options/$padK", $padType [$pad], $padTag [$pad] );

  if ( $padInfoXml   )
    include PAD . 'info/types/xml/level/parms.php';

  if ( $padInfoTrace and $padInfoTraceParms ) {

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