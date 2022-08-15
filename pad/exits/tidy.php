<?php

  if ( $padBuildMode == 'include' )
    $padTidyConfig ['show-body-only'] = true;

  $padTidyOut = new tidy;
  $padTidyOut->parseString($padOutput, $padTidyConfig, 'utf8');
  $padTidyOut->cleanRepair();

  $padOutput = $padTidyOut;

?>