<?php

  global $padTailRequest, $padTailDir, $padTailDump;
  
  if ( $padTailDump )
    padDumpToDir ( '', "$padTailDir/dump");

  if ( $padTailRequest )
    padTailRequestExit ( "$padTailDir/request-exit.json" );

?>