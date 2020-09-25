<?php

  if ( isset($_REQUEST['pad_include']) )
    $pad_tidy_config ['show-body-only'] = true;

  $pad_tidy_out = new tidy;
  $pad_tidy_out->parseString($pad_output, $pad_tidy_config, 'utf8');
  $pad_tidy_out->cleanRepair();

  $pad_output = $pad_tidy_out;

?>