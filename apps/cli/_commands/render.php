<?php

  // pad render <app> [page] [name=value ...]: one page of any application, rendered here
  // and written to stdout - the engine run in this process, as the cli application is, with
  // the page and its request values set the way a URL would set them.
  //
  // A failed page answers what every command-line request answers: the JSON error report
  // (with its template position) and exit status 1. PAD_HOST, when set, is the server the
  // page's own cross-application links and fetches point at ($padHostBase). PAD_LINT, set
  // by pad lint, puts the strict check on whatever the application chose.

  $padRenderApp  = $argv [2] ?? '';
  $padRenderPage = $argv [3] ?? 'index';

  if ( ! cliApp ( $padRenderApp ) )
    return cliFail ( "there is no application named '$padRenderApp' - pad render <app> [page]" );

  if ( str_contains ( $padRenderPage, '=' ) ) {
    $padRenderArgs = array_slice ( $argv, 3 );
    $padRenderPage = 'index';
  } else
    $padRenderArgs = array_slice ( $argv, 4 );

  $_GET = [];

  foreach ( $padRenderArgs as $padRenderArg ) {
    list ( $padRenderKey, $padRenderValue ) = array_pad ( explode ( '=', $padRenderArg, 2 ), 2, '' );
    $_GET [$padRenderKey] = $padRenderValue;
  }

  $_REQUEST = $_GET;

  // A GET, as the URL would have been - pages that test the method find one.

  $_SERVER ['REQUEST_METHOD'] = 'GET';

  // inits/page.php reads the first argument as the page when nothing else names one; the
  // page is named here, so the arguments go.

  $_SERVER ['argv'] = [ $_SERVER ['argv'] [0] ?? 'pad' ];
  $_SERVER ['argc'] = 1;

  if ( getenv ( 'PAD_HOST' ) )
    $padHostBase = getenv ( 'PAD_HOST' );

  if ( getenv ( 'PAD_LINT' ) )
    $padSetConfig = [ 'CheckSyntax' => TRUE ];

  $padApp  = $padRenderApp;
  $padPage = $padRenderPage;
  $padApps = cliHome () . '/apps/';
  $padData = cliHome () . '/DATA/';

  unset ( $padRenderApp, $padRenderPage, $padRenderArgs, $padRenderArg, $padRenderKey, $padRenderValue );

  include cliHome () . '/pad/pad.php';

  return 0;

?>
