<?php

  padTraceOccClose ();

  $padTraceXmlWhere = 'exits';
  
  include pad . 'trace/items/result.php';   
  
  if ( $padTraceEndLvl )
    padTrace ( 'level', 'end' );

  if ( $padTraceStatus )
    padTraceStatus ( $padTraceLevel [$pad] );

  if ( $padTraceLocalChk )
    padTraceCheckLocal ( $padTraceLevel [$pad] );
  
  if ( $padTraceChilds )
    padTraceChilds ( $padTraceLevel [$pad], $padTraceLevelChilds[$pad], 'level' );

  padTraceXmlInitsOpened ();
  padTraceXmlOccurOpened ();
  padTraceXmlExitsOpened ();

  padTraceSet ( 'level', 'end' );

  if ( $pad == 0 )
    $padTraceXmlWhere = 'exits';
  else
    if ( $padOccur [$pad-1] == 0 )
      $padTraceXmlWhere = 'inits';
    else
      $padTraceXmlWhere = 'occurs';

?>