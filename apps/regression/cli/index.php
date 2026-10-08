<?php

  // Test runs the pad command and asserts what each of its commands promises:
  //
  //   render  a page to stdout, request values as name=value, an unknown application refused
  //   new     an application and a page in it, in a scratch PAD_HOME under DATA, refusing to
  //           overwrite, and the new page rendering through the real engine
  //   lint    the pages of lintme/, the broken one named with its template position, the
  //           bracketed route [id] left out - no URL names it
  //   serve   a page fetched from the server it starts, on a free port
  //   export  the regression/site fixture as static files: page links turned into relative
  //           .html files, from a subdirectory too, the assets copied, the outside link kept
  //   test    the _tests of the scratch application - a pass, a failing {assert}, a test
  //           without an answer and --record writing it - and of regression/site, whose
  //           test pages no URL reaches
  //   migrate a migration made by --new in the scratch application, on an SQLite file under
  //           its DATA: --pretend shows it, --seed runs it and the seeder, --status lists it,
  //           a rollback without a down fails, --fresh wants --force
  //   seed    the scratch application's seeder, named without its 01_
  //   down    maintenance of the scratch application: the record with the secret's hash,
  //           the list of what is down, up removing it, an unknown application refused
  //   work    a page of the scratch application queues two jobs, queue counts them, work
  //           --once runs the one and fails the other, --failed names why
  //   schedule the scratch application's _schedule.php listed with its next run, and run at
  //           the minute it is due
  //
  // A plain load only offers the link; verdict.php runs it on every load.

  $tested = isset ( $test ) ? 1 : 0;

  if ( $tested ) {

    // render

    list ( $code1, $out1 ) = cliCheckRun ( [ 'render', 'regression/cli', 'sample' ] );
    list ( $code2, $out2 ) = cliCheckRun ( [ 'render', 'regression/cli', 'sample', 'name=Ann' ] );
    list ( $code3, $out3 ) = cliCheckRun ( [ 'render', 'no/such/app' ] );

    // A queued setting on the first pass - pad lint's strict check - and then the file
    // writer's restart, which queues the web type for the page it restarts into: the
    // second queue has to be applied as well, or the page writes itself again and again.

    list ( $codeR4, $outR4 ) = cliCheckRun ( [ 'render', 'regression/output_file', 'payload', 'payload=1' ], [ 'PAD_LINT' => '1' ] );

    padDeleteDataDir ( DATA . 'regression_output_file' );

    $render = ( $code1 === 0 and trim ( $out1 ) == '<p>Hello nobody</p>'
                and $code2 === 0 and trim ( $out2 ) == '<p>Hello Ann</p>'
                and $code3 === 1
                and $codeR4 === 0 and str_contains ( $outR4, 'wrote the page to disk' ) ) ? 'yes' : 'NO';

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

    // render, once more: an application whose config names the web type, as react and
    // structure do, renders on the command line as any other - plain, and with a queued
    // setting (pad lint) - where the second config pass put 'web' back on the console
    // selector's settings and the page died on an undefined $padWebEtag304.

    mkdir ( "$home/apps/shop/_config", 0755, TRUE );
    file_put_contents ( "$home/apps/shop/_config/config.php", "<?php \$padOutputType = 'web'; ?>" );

    list ( $codeW1, $outW1 ) = cliCheckRun ( [ 'render', 'shop' ], $env );
    list ( $codeW2, $outW2 ) = cliCheckRun ( [ 'render', 'shop' ], $env + [ 'PAD_LINT' => '1' ] );

    unlink ( "$home/apps/shop/_config/config.php" );

    // PAD_HOST is the server a rendered page's own links and SELF:// fetches point at - it
    // was set before the config files ran, and config/config.php put $padHostBase back.

    file_put_contents ( "$home/apps/shop/host.pad", '<p>{$padHost} {$padGo}</p>' );

    list ( $codeH, $outH ) = cliCheckRun ( [ 'render', 'shop', 'host' ], $env + [ 'PAD_HOST' => 'http://example.org/sub/' ] );

    unlink ( "$home/apps/shop/host.pad" );

    $render = ( $render == 'yes'
                and $codeW1 === 0 and str_contains ( $outW1, '<h1>Hello from Shop!</h1>' )
                and $codeW2 === 0 and str_contains ( $outW2, '<h1>Hello from Shop!</h1>' )
                and $codeH  === 0 and str_contains ( $outH,  '<p>http://example.org/sub/ /sub/shop/?</p>' ) ) ? 'yes' : 'NO';

    // test - the scratch application gets its _tests

    $tests = "$home/apps/shop/_tests";

    mkdir ( $tests, 0755, TRUE );

    file_put_contents ( "$tests/good.pad",  '<p>{echo 1 + 1}</p>' );
    file_put_contents ( "$tests/good.txt",  '<p>2</p>' );
    file_put_contents ( "$tests/bad.php",   '<?php $total = 41; ?>' );
    file_put_contents ( "$tests/bad.pad",   '{assert $total eq 42}' );
    file_put_contents ( "$tests/bad.txt",   '' );
    file_put_contents ( "$tests/fresh.pad", 'fresh' );

    list ( $codeT1, $outT1 ) = cliCheckRun ( [ 'test', 'shop' ],              $env );
    list ( $codeT2, $outT2 ) = cliCheckRun ( [ 'test', 'shop', '--record' ],  $env );
    list ( $codeT3, $outT3 ) = cliCheckRun ( [ 'test', 'regression/site' ] );

    $hidden = padCurl ( $padHost . 'regression/site/?_tests/about&padInclude' ) ['result'] ?? '';

    $test = ( $codeT1 === 1
              and str_contains ( $outT1, 'ok    good' )
              and str_contains ( $outT1, 'FAIL  bad' )
              and str_contains ( $outT1, 'assert failed: $total eq 42' )
              and str_contains ( $outT1, 'NEW   fresh' )
              and $codeT2 === 1 and str_contains ( $outT2, 'made  fresh' )
              and trim ( (string) @file_get_contents ( "$tests/fresh.txt" ) ) == 'fresh'
              and $codeT3 === 0 and str_contains ( $outT3, '4 tests, 0 failed' )
              and $hidden == '404' ) ? 'yes' : 'NO';

    // migrate and seed - the scratch application gets an SQLite database under the scratch
    // DATA/, a migration made by --new, and a seeder

    file_put_contents ( "$home/apps/shop/.env", "padSqlDriver=sqlite\npadSqlDatabase=shop.sqlite\n" );
    file_put_contents ( "$home/apps/shop/_config/config.php",
      "<?php \$padSqlDriver = padEnv ( 'padSqlDriver' ); \$padSqlDatabase = padEnv ( 'padSqlDatabase' ); ?>" );

    mkdir ( "$home/apps/shop/_seeds", 0755, TRUE );
    file_put_contents ( "$home/apps/shop/_seeds/01_notes.php",
      "<?php padFakeSeed ( 1 ); padFactory ( 'notes', 3, fn ( \$i ) => [ 'text' => padFakeWord () ] ); ?>" );

    list ( $codeM1, $outM1 ) = cliCheckRun ( [ 'migrate', 'shop', '--new=create_notes' ], $env );

    $made = $home . '/' . trim ( $outM1 );

    if ( $codeM1 === 0 and is_file ( $made ) )
      file_put_contents ( $made, "create table notes (id integer primary key, text varchar(20));\n" );

    list ( $codeM2, $outM2 ) = cliCheckRun ( [ 'migrate', 'shop', '--pretend' ],          $env );
    list ( $codeM3, $outM3 ) = cliCheckRun ( [ 'migrate', 'shop', '--seed' ],             $env );
    list ( $codeM4, $outM4 ) = cliCheckRun ( [ 'migrate', 'shop', '--status' ],           $env );
    list ( $codeM5, $outM5 ) = cliCheckRun ( [ 'seed', 'shop', 'notes' ],                 $env );
    list ( $codeM6, $outM6 ) = cliCheckRun ( [ 'migrate', 'shop', '--rollback' ],         $env );
    list ( $codeM7 )         = cliCheckRun ( [ 'migrate', 'shop', '--fresh' ],            $env );
    list ( $codeM8, $outM8 ) = cliCheckRun ( [ 'migrate', 'shop', '--fresh', '--force' ], $env );

    $migrate = ( $codeM1 === 0 and preg_match ( '#^apps/shop/_migrations/\d{4}_\d{2}_\d{2}_\d{6}_create_notes\.sql$#', trim ( $outM1 ) )
                 and $codeM2 === 0 and str_contains ( $outM2, 'create table notes (id integer primary key, text varchar(20));' )
                 and $codeM3 === 0 and str_contains ( $outM3, '_create_notes' ) and str_contains ( $outM3, 'seeded      01_notes' )
                 and $codeM4 === 0 and preg_match ( '/yes  1      \S+_create_notes/', $outM4 )
                 and $codeM6 === 1
                 and $codeM7 === 1
                 and $codeM8 === 0 and str_contains ( $outM8, 'migrated' ) ) ? 'yes' : 'NO';

    $seed = ( $codeM5 === 0 and trim ( $outM5 ) == 'seeded      01_notes' ) ? 'yes' : 'NO';

    // down and up - maintenance of the scratch application, in the scratch home's DATA:
    // down writes the record with the secret's hash, the list names it, up removes it and
    // a second up has nothing to remove; an application that is not there is refused

    list ( $codeD1, $outD1 ) = cliCheckRun ( [ 'down', 'shop', '--secret=letmein42', '--retry=5', '--message=Back soon' ], $env );
    $downRecord = json_decode ( (string) @file_get_contents ( "$home/DATA/maintenance/shop.json" ), TRUE ) ?: [];
    list ( $codeD2, $outD2 ) = cliCheckRun ( [ 'down' ], $env );
    list ( $codeD3 )         = cliCheckRun ( [ 'up', 'shop' ], $env );
    list ( $codeD4 )         = cliCheckRun ( [ 'up', 'shop' ], $env );
    list ( $codeD5 )         = cliCheckRun ( [ 'down', 'no/such/app' ], $env );
    list ( $codeD6, $outD6 ) = cliCheckRun ( [ 'down' ], $env );

    $down = ( $codeD1 === 0 and str_contains ( $outD1, 'Let yourself through: ?letmein42' )
              and ( $downRecord ['retry'] ?? 0 ) === 5 and ( $downRecord ['message'] ?? '' ) === 'Back soon'
              and ( $downRecord ['secret'] ?? '' ) === hash ( 'sha256', 'letmein42' )
              and $codeD2 === 0 and str_contains ( $outD2, 'shop' ) and str_contains ( $outD2, 'with a secret - Back soon' )
              and $codeD3 === 0 and ! file_exists ( "$home/DATA/maintenance/shop.json" )
              and $codeD4 === 1 and $codeD5 === 1
              and $codeD6 === 0 and trim ( $outD6 ) == 'No application is down' ) ? 'yes' : 'NO';

    // work, queue and schedule - job handlers, a page that queues, a schedule

    mkdir ( "$home/apps/shop/_jobs", 0755, TRUE );
    file_put_contents ( "$home/apps/shop/_jobs/note.php", "<?php padLog ( 'note {n}', 'info', [ 'n' => \$n ] ); ?>" );
    file_put_contents ( "$home/apps/shop/_jobs/fail.php", "<?php return FALSE; ?>" );
    file_put_contents ( "$home/apps/shop/enqueue.php", "<?php padQueue ( 'note', [ 'n' => 1 ] ); padQueue ( 'fail', [], tries: 1 ); ?>" );
    file_put_contents ( "$home/apps/shop/enqueue.pad", "queued" );
    file_put_contents ( "$home/apps/shop/_schedule.php", "<?php return [ [ 'every' => 'day at 03:00', 'job' => 'note', 'data' => [ 'n' => 2 ] ] ]; ?>" );

    list ( $codeQ1 )         = cliCheckRun ( [ 'render', 'shop', 'enqueue' ], $env );
    list ( $codeQ2, $outQ2 ) = cliCheckRun ( [ 'queue', 'shop' ], $env );
    list ( $codeQ3, $outQ3 ) = cliCheckRun ( [ 'work', 'shop', '--once' ], $env );
    list ( $codeQ4, $outQ4 ) = cliCheckRun ( [ 'queue', 'shop', '--failed' ], $env );
    list ( $codeQ5, $outQ5 ) = cliCheckRun ( [ 'schedule', 'shop', '--list', '--at=2026-10-12 08:00' ], $env );
    list ( $codeQ6, $outQ6 ) = cliCheckRun ( [ 'schedule', 'shop', '--at=2026-10-13 03:00' ], $env );

    $work     = ( $codeQ1 === 0 and $codeQ2 === 0 and preg_match ( '/default\s+2\s+0\s+0/', $outQ2 )
                  and $codeQ3 === 0 and preg_match ( '/done\s+note/', $outQ3 ) and preg_match ( '/failed\s+fail/', $outQ3 )
                  and $codeQ4 === 0 and str_contains ( $outQ4, 'the handler returned FALSE' ) ) ? 'yes' : 'NO';

    $schedule = ( $codeQ5 === 0 and str_contains ( $outQ5, '0 3 * * *' ) and str_contains ( $outQ5, '2026-10-13 03:00' )
                  and $codeQ6 === 0 and preg_match ( '/done\s+note/', $outQ6 ) ) ? 'yes' : 'NO';

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

    // export

    $dir = DATA . 'cli-export-' . padRandomString ( 8 );

    list ( $code9, $out9 ) = cliCheckRun ( [ 'export', 'regression/site', $dir ] );

    $index = (string) @file_get_contents ( "$dir/index.html" );
    $intro = (string) @file_get_contents ( "$dir/docs/intro.html" );
    $about = (string) @file_get_contents ( "$dir/about.html" );

    $export = ( $code9 === 0 and str_contains ( $out9, '3 pages and 2 assets exported' )
                and substr_count ( $index, 'href="about.html"' ) == 2
                and str_contains ( $index, 'href="docs/intro.html#start"' )
                and str_contains ( $index, 'href="style.css"' )
                and str_contains ( $index, 'href="https://example.com/"' )
                and str_contains ( $intro, 'href="../index.html"' )
                and str_contains ( $intro, 'src="../img/logo.svg"' )
                and str_contains ( $about, 'href="index.html"' )
                and file_exists ( "$dir/style.css" ) and file_exists ( "$dir/img/logo.svg" )
                and ! file_exists ( "$dir/index.php" ) ) ? 'yes' : 'NO';

    padDeleteDataDir ( $dir );

    // The same copy made against a server mounted under /sub/: the page's own links -
    // {$pad}about is $padGo - carry that mount, and are rewritten like the rest. $padGo
    // kept the root mount PAD_HOST did not reach, and /regression/site/?about stayed in
    // the copy, a link to nowhere.

    $dirHost = DATA . 'cli-export-' . padRandomString ( 8 );

    list ( $codeE2 ) = cliCheckRun ( [ 'export', 'regression/site', $dirHost ], [ 'PAD_HOST' => 'http://example.org/sub/' ] );

    $indexHost = (string) @file_get_contents ( "$dirHost/index.html" );

    $export = ( $export == 'yes' and $codeE2 === 0
                and substr_count ( $indexHost, 'href="about.html"' ) == 2 ) ? 'yes' : 'NO';

    padDeleteDataDir ( $dirHost );

  }

?>
