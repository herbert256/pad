<?php

  if ( $pBuild_mode == 'include' )
    $pTidy_config ['show-body-only'] = true;

  $pTidy_out = new tidy;
  $pTidy_out->parseString($pOutput, $pTidy_config, 'utf8');
  $pTidy_out->cleanRepair();

  $pOutput = $pTidy_out;

?>