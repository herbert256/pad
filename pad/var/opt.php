<?php

  pTiming_start ('var');

  include 'inits.php';

  $pOpts = [];

  foreach ( $pData_default_start as $pV )
    $pOpts [] = $pV;

  foreach ( $pExpl as $pV )
    $pOpts [] = trim($pV);

  foreach ( $pData_default_end as $pV )
    $pOpts [] = $pV;

  $pVal = pVar_opts ($pVal, $pOpts);

  return include 'exits.php';

?>