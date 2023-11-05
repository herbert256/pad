<?php

  $padTraceLevelChilds [$pad] = 0;
  $padTraceOccurChilds [$pad] = [];

  padTraceSet ( 'level', 'start' );

  if ( $padTraceStartLvl )
    padTrace ( 'level', 'start', 
      ' tag='  . $padTag     [$pad] . 
      ' type=' . $padType    [$pad] . 
      ' pair=' . $padPair    [$pad] . 
      ' parm=' . $padPrmType [$pad]
    );

?>