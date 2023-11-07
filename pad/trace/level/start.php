<?php

  padTraceInit ( $pad );

  padTraceXmlExitsOpened ();  

  padTraceSet ( 'level', 'start' );

  $padTraceXmlWhere [$pad] = 'inits';

  if ( $padTraceStartLvl )
    padTrace ( 'level', 'start', 
      ' tag='  . $padTag     [$pad] . 
      ' type=' . $padType    [$pad] . 
      ' pair=' . $padPair    [$pad] . 
      ' parm=' . $padPrmType [$pad]
    );  

?>