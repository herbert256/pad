<?php

  pTiming_start ('var');

  include 'inits.php';

  $pOpts = $pExpl;
  $pVal  = pVar_opts ($pVal, $pOpts);
  $pVal  = str_replace ( '}', '&close;', $pVal );

  return include 'exits.php';

?>