<?php

  if ( $padTraceTypes ['flags'] )
    padTrace ( 'level', 'flags', 
      ' hit='   . $padHit   [$pad] . 
      ' else='  . $padElse  [$pad] . 
      ' null='  . $padNull  [$pad] . 
      ' array=' . $padArray [$pad] . 
      ' text='  . $padText  [$pad] . 
      ' count=' . count ( $padData [$pad] )
    );

  if ( $padTraceTypes ['true'] )
    padTrace ( 'level', 'true',  $padTrue  [$pad] ); 

  if ( $padTraceTypes ['false'] )
    padTrace ( 'level', 'false', $padFalse [$pad] ); 

  if ( ! $padTraceTypes ['tree'] )
    return;

  if ( $padTraceTypes ['content'] )
    padFilePutContents ( "$padTraceDir/content.pad", $padTraceContent );

  if ( $padTraceTypes ['data'] )
    if ( ! padIsDefaultData ( $padData [$pad] ) )
      padFilePutContents ( "$padTraceDir/data.json",   $padData [$pad] );

?>