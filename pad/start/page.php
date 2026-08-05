<?php

  // Body of the {page} tag: renders another page of this application inside the current
  // one, by building it with build/build.php in a nested engine pass.
  //
  // With an app= parameter the page lives in a different application and cannot be built
  // in process, so the work is handed to start/pad/pageApp.php instead. Otherwise the four
  // globals that say which page is being built - $padPage, $padInclude, $padDir, $padPath -
  // are parked in $padStrPag[$pad], pointed at the requested page for the duration of the
  // nested pass, and put back before the tag's value is returned. include= defaults to TRUE,
  // so the embedded page renders bare, without picking up the _inits/_exits wrappers again.

  if ( padTagParm ( 'app' ) )
    return include PAD . 'start/pad/pageApp.php';

  $padStrPag [$pad] [0] = $padPage;
  $padStrPag [$pad] [1] = $padInclude;
  $padStrPag [$pad] [2] = $padDir;
  $padStrPag [$pad] [3] = $padPath;

  $padPage    = $padParm;
  $padInclude = padTagParm ( 'include', TRUE );
  $padDir     = padDir ();
  $padPath    = padPath ();

  $padStrBld = 'page';
  $padStrCod = '';
  $padStrRet = include PAD . 'start/pad/parms.php';

  $padPage    = $padStrPag [$pad] [0];
  $padInclude = $padStrPag [$pad] [1];
  $padDir     = $padStrPag [$pad] [2];
  $padPath    = $padStrPag [$pad] [3];

  return $padStrRet;

?>