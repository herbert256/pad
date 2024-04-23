<?php

  if ( padTrace and $padTraceParms ) {

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

  if ( padXml  ) include pad . 'info/types/xml/level/parms.php';  
  if ( padXref ) include pad . 'info/types/xref/items/parms.php';
  if ( padXweb ) include pad . 'info/types/xweb/items/parms.php';

?>