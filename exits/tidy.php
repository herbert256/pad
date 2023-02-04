<?php

  if ( $GLOBALS ['padTrace'] ) 
    padFilePutContents ( $GLOBALS ['padTraceDir'] . "/tidy_in.json", $padOutput );

  if ( $padBuildMode == 'include' )
    $padTidyConfig ['show-body-only'] = true;

  $padTidyOut = new tidy;
  $padTidyOut->parseString($padOutput, $padTidyConfig, 'utf8');
  $padTidyOut->cleanRepair();

  if ( $GLOBALS ['padTrace'] ) 
    padFilePutContents ( $GLOBALS ['padTraceDir'] . "/tidy_out.json", $padTidyOut );

  $padOutput = $padTidyOut->value;

?>