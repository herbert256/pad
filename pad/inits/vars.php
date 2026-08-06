<?php

  // Declares and zeroes the engine's request-scoped globals.
  //
  // Most important is $pad, the current nesting level, which starts at -1 meaning "no level
  // yet"; inits/level.php then opens the root level. The rest are the accumulating request
  // state: the output being built ($padOutput, $padLen, $padEtag, $padStop), the restart
  // request, the counters used by strings and info, and the caches for data and
  // providers.
  //
  // The pqStore / padLastPush / padLastPull guards keep a sequence store alive across a
  // restart, and $padInclude records how the request arrived.
  //
  // The four stores of padStrSto are declared here rather than on first write. A nested pass
  // that runs inside a PHP function body - padCode() and padSandbox() - binds the globals it
  // can see at the moment it opens, so a store that does not exist yet is never bound, and
  // start/start/resetPad.php cannot empty what is not set either. Writing {data 'x'} then
  // reading {x} would land in two different scopes.

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
  $padInclude   = isset ( $_REQUEST ['padInclude'] ) ? TRUE : FALSE;
  $padStrCnt    = -1;
  $padStrFunCnt = 0;
  $padInfo      = '';
  $padInfoCnt   = 0;

  $padData      = [];
  $padProviders = [];

  if ( ! isset ( $pqStore )     ) $pqStore     = [];
  if ( ! isset ( $padLastPush ) ) $padLastPush = '';
  if ( ! isset ( $padLastPull ) ) $padLastPull = '';

  $padBetweenOrg   = $padBetweenOrg   ?? '';
  $padDataStore    = $padDataStore    ?? [];
  $padContentStore = $padContentStore ?? [];
  $padBoolStore    = $padBoolStore    ?? [];

?>