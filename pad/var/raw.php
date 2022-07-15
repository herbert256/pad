<?php

  pad_timing_start ('var');

  include 'inits.php';

  $pad_opts = $pad_expl;
  $pad_val  = pad_var_opts ($pad_val, $pad_opts);
  $pad_val  = str_replace ( '}', '&close;', $pad_val );

  return include 'exits.php';

?>