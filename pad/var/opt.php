<?php

  pTiming_start ('var');

  include 'inits.php';

  $pOpts = [];

  foreach ( $pData_default_start as $pad_v )
    $pOpts [] = $pad_v;

  foreach ( $pExpl as $pad_v )
    $pOpts [] = trim($pad_v);

  foreach ( $pData_default_end as $pad_v )
    $pOpts [] = $pad_v;

  $pad_val = pad_var_opts ($pad_val, $pOpts);

  return include 'exits.php';

?>