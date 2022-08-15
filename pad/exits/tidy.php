<?php

  if ( $padBuild_mode == 'include' )
    $padTidy_config ['show-body-only'] = true;

  $padTidy_out = new tidy;
  $padTidy_out->parseString($padOutput, $padTidy_config, 'utf8');
  $padTidy_out->cleanRepair();

  $padOutput = $padTidy_out;

?>