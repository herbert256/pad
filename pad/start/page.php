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

  // A page that is not there took the whole response with it: the not-found error rose
  // inside the nested pass and the request answered an empty 200. Strict mode names the
  // page; the lenient walk gives the tag an empty value and the rest of the page lives.
  //
  // The page is named from the application root and resolved as the router resolves a
  // URL (lib/page.php): a .html page counts, and a directory is its index.

  $padStrPagRoute = padPageRoute ( $padParm );
  $padStrPagName  = $padStrPagRoute ['page'] ?? FALSE;

  if ( ! $padStrPagName ) {

    if ( $padCheckSyntax )
      padError ( "there is no page named '$padParm'" );

    return '';

  }

  // A clean URL route, {page 'products/42'}, binds its segments as the request does - as
  // variables the embedded page reads (lib/route.php).

  padRouteBind ( $padStrPagRoute ['vars'] );

  $padStrPag [$pad] [0] = $padPage;
  $padStrPag [$pad] [1] = $padInclude;
  $padStrPag [$pad] [2] = $padDir;
  $padStrPag [$pad] [3] = $padPath;
  $padStrPag [$pad] [4] = $padGuardNested ?? FALSE;

  // The included page's directory guards still decide (build/guards.php), but a refusal
  // makes this page render as nothing instead of refusing the whole request.

  $padGuardNested = TRUE;

  $padPage    = $padStrPagName;
  $padInclude = padTagParm ( 'include', TRUE );
  $padDir     = padDir ();
  $padPath    = padPath ();

  $padStrBld = 'page';
  $padStrCod = '';
  $padStrRet = include PAD . 'start/pad/parms.php';

  $padPage        = $padStrPag [$pad] [0];
  $padInclude     = $padStrPag [$pad] [1];
  $padDir         = $padStrPag [$pad] [2];
  $padPath        = $padStrPag [$pad] [3];
  $padGuardNested = $padStrPag [$pad] [4];

  return $padStrRet;

?>
