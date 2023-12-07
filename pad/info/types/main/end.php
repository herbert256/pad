<?php

  global $padMainRequest, $padInfoDir, $padMainDump;
  
  if ( $GLOBALS ['padMainSession'] )
    padMainSessionEnd ();

  if ( $GLOBALS ['padMainOutput'] )
    padMainData ();
  
  if ( $padMainDump )
    padDumpToDir ( '', "$padInfoDir/dump");

  if ( $padMainRequest )
    padMainRequestExit ( "$padInfoDir/request-exit.json" );

?>