<?php

  padTraceInit ( $pad );

  if ( $padTraceXmlInitsOpened [$pad] and ! $padTraceXmlInitsClosed [$pad] )
    if ( $padOccur [$pad-1] <> 0 )
      padTraceXmlInitsOpened ();

  padTraceXmlExitsOpened ();  

  if ( $padOccur [$pad-1] == 0 )
    $padTraceXmlWhere = 'inits';
  else
    $padTraceXmlWhere = 'occurs';

  padTraceSet ( 'level', 'start' );

  if ( $padTraceStartLvl )
    padTrace ( 'level', 'start', 
      ' tag='  . $padTag     [$pad] . 
      ' type=' . $padType    [$pad] . 
      ' pair=' . $padPair    [$pad] . 
      ' parm=' . $padPrmType [$pad]
    );  

?>