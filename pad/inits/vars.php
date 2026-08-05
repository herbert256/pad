<?php

  // Declares and zeroes the engine's request-scoped globals.
  //
  // Most important is $pad, the current nesting level, which starts at -1 meaning "no level
  // yet"; inits/level.php then opens the root level. The rest are the accumulating request
  // state: the output being built ($padOutput, $padLen, $padEtag, $padStop), the restart
  // request, the counters used by strings, evals and info, and the caches for data and
  // providers.
  //
  // The pqStore / padLastPush / padLastPull guards keep a sequence store alive across a
  // restart, and $padPost / $padInclude record how the request arrived.

  $pad          = -1;
  $padLvlId     = 0;
  $padAppTime   = 0;
  $padRestart   = '';
  $padOutput    = '';
  $padStop      = '000';
  $padEtag      = '';
  $padLen       = 0;
  $padTime      = $_SERVER ['REQUEST_TIME'];
  $padCacheStop = 0;
  $padPageLevel = [];
  $padInclude   = isset ( $_REQUEST ['padInclude'] ) ? TRUE : FALSE;
  $padStrCnt    = -1;
  $padStrFunCnt = 0;
  $padInfo      = '';
  $padInfoCnt   = 0;
  $padEvalCnt   = -1;

  $padData         = [];
  $padProviders    = [];
  $padProvidersLvl = [];

  if ( ! isset ( $pqStore )     ) $pqStore     = [];
  if ( ! isset ( $padLastPush ) ) $padLastPush = '';
  if ( ! isset ( $padLastPull ) ) $padLastPull = '';

  $padPost = ( isset ( $_SERVER['REQUEST_METHOD'] ) and $_SERVER['REQUEST_METHOD'] == 'POST' );

?>