<?php

  // Test runs the pad command and asserts what each of its commands promises:
  //
  //   render  a page to stdout, request values as name=value, an unknown application refused
  //   new     an application and a page in it, in a scratch PAD_HOME under DATA, refusing to
  //           overwrite, and the new page rendering through the real engine
  //   lint    the pages of lintme/, the broken one named with its template position
  //   serve   a page fetched from the server it starts, on a free port
  //
  // A plain load only offers the link; verdict.php runs it on every load.

  $tested = isset ( $test ) ? 1 : 0;

  if ( $tested ) {

    // render

    list ( $code1, $out1 ) = cliCheckRun ( [ 'render', 'regression/cli', 'sample' ] );
    list ( $code2, $out2 ) = cliCheckRun ( [ 'render', 'regression/cli', 'sample', 'name=Ann' ] );
    list ( $code3, $out3 ) = cliCheckRun ( [ 'render', 'no/such/app' ] );

    $render = ( $code1 === 0 and trim ( $out1 ) == '<p>Hello nobody</p>'
                and $code2 === 0 and trim ( $out2 ) == '<p>Hello Ann</p>'
                and $code3 === 1 ) ? 'yes' : 'NO';

    // new - in a scratch home whose engine and _common are the real ones

    $home = DATA . 'cli-test-' . padRandomString ( 8 );

    mkdir ( "$home/apps", 0755, TRUE );
    mkdir ( "$home/www",  0755, TRUE );
    mkdir ( "$home/DATA", 0755, TRUE );

    symlink ( PAD,                        "$home/pad" );
    symlink ( rtrim ( COMMON, '/' ),      "$home/apps/_common" );

    $env = [ 'PAD_HOME' => $home ];

    list ( $code4 ) = cliCheckRun ( [ 'new', 'shop' ],             $env );
    list ( $code5 ) = cliCheckRun ( [ 'new', 'shop/orders/list' ], $env );
    list ( $code6 ) = cliCheckRun ( [ 'new', 'shop/orders/list' ], $env );
    list ( $code7, $out7 ) = cliCheckRun ( [ 'render', 'shop', 'orders/list' ], $env );

    $new = ( $code4 === 0 and $code5 === 0 and $code6 === 1
             and file_exists ( "$home/apps/shop/index.pad" ) and file_exists ( "$home/www/shop/index.php" )
             and file_exists ( "$home/apps/shop/orders/list.php" )
             and $code7 === 0 and str_contains ( $out7, '<h1>List</h1>' ) ) ? 'yes' : 'NO';

    padDeleteDataDir ( $home );

    // lint

    list ( $code8, $out8 ) = cliCheckRun ( [ 'lint', 'regression/cli', 'lintme' ] );

    $lint = ( $code8 === 1
              and str_contains ( $out8, "FAIL  lintme/broken  Field '\$nmae' not found" )
              and str_contains ( $out8, 'apps/regression/cli/lintme/broken.pad:2:5  {$nmae}  - did you mean $name?' )
              and str_contains ( $out8, 'ok    lintme/good' )
              and str_contains ( $out8, '2 pages, 1 failed' ) ) ? 'yes' : 'NO';

    // serve

    $serve = ( cliCheckServe () == '<p>Hello Bob</p>' ) ? 'yes' : 'NO';

  }

?>
