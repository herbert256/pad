<?php

  if ( ! $padTrace )
    return;

  pFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

  pFile_put_contents ( $padLevelDir [$pad] . "/end.json",      pTraceGetLevel ($pad) );
  pFile_put_contents ( $padLevelDir [$pad] . "/app-end.json",  $padFapp );
  pFile_put_contents ( $padLevelDir [$pad] . "/pad-end.json",  $padFpad ); 
  pFile_put_contents ( $padLevelDir [$pad] . "/result.html",   $padResult [$pad] );
  
?>