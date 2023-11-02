<?php

  if ( $pad == 0 )
    $padTraceLevelDir [$pad] = $padTraceBase .'/page' ;
  else
    $padTraceLevelDir [$pad] = $padTraceLevelDir [$pad-1] . '/' . $padTraceId [$i] . '-' .  padFileCorrect ( $padTag [$i] );

  if ( $pad == )
  $padTraceId           [$pad] = 0;
  $padTraceOccurId      [$pad] = 0;
  $padTraceChilds       [$pad] = 0;
  $padTraceOccurs       [$pad] = 0;
  $padTraceLevelDir     [$pad] = '';
  $padTraceOccurDirName [$pad] = [];
  $padTraceOccurHasDir  [$pad] = [];

  padTrace ( 'level', 'start', 
    ' tag='  . $padTag [$pad] . 
    ' type=' . $padType    [$pad] . 
    ' pair=' . $padPair    [$pad] . 
    ' parm=' . $padPrmType [$pad]
  );

?>