<?php

  $padTraceLevelChilds [$pad] = 0;
  $padTraceOccurChilds [$pad] = [];

  $padTraceLevel [$pad]       = $padTraceBase;
  $padTraceOccur [$pad]       = [];
  $padTraceOccur [$pad] [0]   = '';

  padTrace ( 'level', 'start', 
    ' tag='  . $padTag     [$pad] . 
    ' type=' . $padType    [$pad] . 
    ' pair=' . $padPair    [$pad] . 
    ' parm=' . $padPrmType [$pad]
  );

?>