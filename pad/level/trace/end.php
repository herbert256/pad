<?php

  if ( ! $padTrace )
    return;

  padTraceFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

  padFilePutContents ( $padLevelDir [$pad] . "/end.json",      padTraceGetLevel ($pad) );
  padFilePutContents ( $padLevelDir [$pad] . "/app-end.json",  $padFapp );
  padFilePutContents ( $padLevelDir [$pad] . "/pad-end.json",  $padFpad ); 
  padFilePutContents ( $padLevelDir [$pad] . "/result.html",   $padResult [$pad] );

  if ( $pad > 1 )
    $padLevelDir [$pad] = $padLevelDir [$pad-1];
  else
    $padLevelDir [$pad] = $padTraceDir;

?>