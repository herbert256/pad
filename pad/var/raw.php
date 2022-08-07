<?php

  pTiming_start ('var');

  include 'inits.php';

  $pOpts = $pExpl;
  $pad_val  = pad_var_opts ($pad_val, $pOpts);
  $pad_val  = str_replace ( '}', '&close;', $pad_val );

  return include 'exits.php';

?>