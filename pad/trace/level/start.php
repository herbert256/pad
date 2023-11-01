<?php

  padTrace ( 'level', 'start', 
    ' tag='  . $padTag [$pad] . 
    ' type=' . $padType    [$pad] . 
    ' pair=' . $padPair    [$pad] . 
    ' parm=' . $padPrmType [$pad]
  );

  $padTraceId           [$pad] = 0;
  $padTraceOccurId      [$pad] = 0;
  $padTraceChilds       [$pad] = 0;
  $padTraceOccurs       [$pad] = 0;
  $padTraceLevelDirName [$pad] = $padTraceDir;
  $padTraceOccurDirName [$pad] = [];
  $padTraceOccurHasDir  [$pad] = [];

?>