<?php

  include pad . 'trace/items/resultOcc.php';     

  if ( $padTraceEndOcc )
    padTrace ( 'occur', 'end' );

  if ( $padTraceLocalChk )
    padTraceCheckLocal ( $padTraceOccur [$pad] [$padOccur[$pad]] );
  
  if ( $padTraceChilds )
    padTraceChilds ( $padTraceOccur [$pad] [$padOccur[$pad]], $padTraceOccurChilds[$pad] [$padOccur[$pad]], 'occur' );

  $padTraceXmlWhere [$pad] = 'occur-end';

  padTraceSet ( 'occur', 'end' );
   
?>