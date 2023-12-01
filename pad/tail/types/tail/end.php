<?php

  global $padTailRequest, $padTailDir, $padTailDump;
  
  if ( $GLOBALS ['padTailSession'] )
    padTailSessionEnd ();

  if ( $GLOBALS ['padTailOutput'] )
    padTailData ();
  
  if ( $padTailDump )
    padDumpToDir ( '', "$padTailDir/dump");

  if ( $padTailRequest )
    padTailRequestExit ( "$padTailDir/request-exit.json" );

?>