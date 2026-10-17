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
  //           .html files, from a subdirectory too, the assets copied, the outside link kept;
  //           and a scratch application with its {img} thumbnails, srcset and {video} poster
  //   test    the _tests of the scratch application - a pass, a failing {assert}, a test
  //           without an answer and --record writing it, a pattern over a page of 1.6 MB, a
  //           pattern that cannot be run, a long line cut, bodies that read as a pattern or
  //           a status recorded as the pattern of that body - and of regression/site, whose
  //           test pages no URL reaches
  //   types   a page's variables and JSON answer as TypeScript, its sample captured first,
  //           to standard output and to the file --out names
  //   migrate a migration made by --new in the scratch application, on an SQLite file under
  //           its DATA: --pretend shows it, --seed runs it and the seeder, --status lists it,
  //           a rollback without a down fails, --fresh wants --force and refuses --pretend
  //   seed    the scratch application's seeder, named without its 01_
  //   down    maintenance of the scratch application: the record with the secret's hash,
  //           the list of what is down, up removing it, an unknown application refused
  //   work    a page of the scratch application queues two jobs, queue counts them, work
  //           --once runs the one and fails the other, --failed names why
  //   schedule the scratch application's _schedule.php listed with its next run, and run at
  //           the minute it is due
  //   pack    an application that is not there, a panel and an option it does not know -
  //           refused before node, which the packing itself needs, is asked
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

    // types - a page of the scratch application with data and a JSON answer: pad types
    // captures its sample, then writes its variables and its answer as TypeScript, to
    // standard output and with --out to a file

    file_put_contents ( "$home/apps/shop/orders.php", '<?php $orders = [ [ "id" => 1, "total" => 9.5 ], [ "id" => 2, "total" => 12, "gift" => true ] ]; $padExpose = [ "orders" ]; ?>' );
    file_put_contents ( "$home/apps/shop/orders.pad", '{orders}{$id} {/orders}' );

    list ( $codeY1, $outY1 ) = cliCheckRun ( [ 'types', 'shop', 'orders' ], $env );
    list ( $codeY2 )         = cliCheckRun ( [ 'types', 'shop', "--out=$home/types/pad.d.ts" ], $env );
    list ( $codeY3 )         = cliCheckRun ( [ 'types', 'no/such/app' ], $env );

    $written = (string) @file_get_contents ( "$home/types/pad.d.ts" );

    // two pages whose names make one type name are refused, both named

    foreach ( [ 'order_list', 'order-list' ] as $typesOne ) {
      file_put_contents ( "$home/apps/shop/$typesOne.php", '<?php $n = 1; ?>' );
      file_put_contents ( "$home/apps/shop/$typesOne.pad", '{$n}' );
    }

    list ( $codeY4, , $errY4 ) = cliCheckRun ( [ 'types', 'shop', 'order_list', 'order-list' ], $env );

    foreach ( [ 'order_list', 'order-list' ] as $typesOne )
      array_map ( 'unlink', [ "$home/apps/shop/$typesOne.php", "$home/apps/shop/$typesOne.pad" ] );

    $types = ( $codeY1 === 0
               and str_contains ( $outY1, 'export interface OrdersVars {' )
               and str_contains ( $outY1, "  orders: {\n    id: number;\n    total: number;\n    gift?: boolean;\n  }[];" )
               and str_contains ( $outY1, 'export interface OrdersAnswer {' )
               and file_exists ( "$home/apps/shop/_samples/orders.json" )
               and $codeY2 === 0 and str_contains ( $written, 'export interface OrdersAnswer {' )
               and $codeY3 === 1
               and $codeY4 === 1 and str_contains ( $errY4, "'order_list' and 'order-list' both make the type name OrderListVars" ) ) ? 'yes' : 'NO';

    // test - the scratch application gets its _tests

    $tests = "$home/apps/shop/_tests";

    mkdir ( $tests, 0755, TRUE );

    file_put_contents ( "$tests/good.pad",  '<p>{echo 1 + 1}</p>' );
    file_put_contents ( "$tests/good.txt",  '<p>2</p>' );
    file_put_contents ( "$tests/bad.php",   '<?php $total = 41; ?>' );
    file_put_contents ( "$tests/bad.pad",   '{assert $total eq 42}' );
    file_put_contents ( "$tests/bad.txt",   '' );
    file_put_contents ( "$tests/fresh.pad", 'fresh' );

    // a pattern over a page of 1.6 MB passes as in the suites, with their backtrack limit; a
    // pattern that cannot be run says so; a failing one-line body of 5000 characters is cut

    file_put_contents ( "$tests/big.php",   '<?php $big = str_repeat ( "x<p>", 400000 ); ?>' );
    file_put_contents ( "$tests/big.pad",   '{echo $big}END' );
    file_put_contents ( "$tests/big.txt",   '/^[\s\S]*?x[\s\S]*?END$/' );
    file_put_contents ( "$tests/odd.pad",   'odd' );
    file_put_contents ( "$tests/odd.txt",   '/(/' );
    file_put_contents ( "$tests/wide.php",  '<?php $wide = str_repeat ( "y", 5000 ); ?>' );
    file_put_contents ( "$tests/wide.pad",  '{$wide}' );
    file_put_contents ( "$tests/wide.txt",  'z' );

    // a body that reads as another answer form - /pad/demo/ and /a+/ as patterns, HTTP 200
    // as a status - is recorded as the pattern of that exact body: it passes for it, and /a+/
    // passes for no other page with an a in it

    mkdir ( "$tests/rec", 0755, TRUE );

    file_put_contents ( "$tests/rec/path.pad",   '/pad/demo/' );
    file_put_contents ( "$tests/rec/plus.pad",   '/a+/' );
    file_put_contents ( "$tests/rec/status.pad", "HTTP 200\nok" );

    list ( $codeT1, $outT1 ) = cliCheckRun ( [ 'test', 'shop' ],              $env );
    list ( $codeT2, $outT2 ) = cliCheckRun ( [ 'test', 'shop', '--record' ],  $env );
    list ( $codeT3, $outT3 ) = cliCheckRun ( [ 'test', 'regression/site' ] );

    list ( $codeT4, $outT4 ) = cliCheckRun ( [ 'test', 'shop', 'rec' ], $env );

    file_put_contents ( "$tests/rec/plus.pad", 'a' );

    list ( $codeT5 ) = cliCheckRun ( [ 'test', 'shop', 'rec/plus' ], $env );

    $hidden = padCurl ( $padHost . 'regression/site/?_tests/about&padInclude' ) ['result'] ?? '';

    $test = ( $codeT1 === 1
              and str_contains ( $outT1, 'ok    good' )
              and str_contains ( $outT1, 'FAIL  bad' )
              and str_contains ( $outT1, 'assert failed: $total eq 42' )
              and str_contains ( $outT1, 'NEW   fresh' )
              and str_contains ( $outT1, 'ok    big' )
              and str_contains ( $outT1, 'FAIL  odd - the pattern could not be run' )
              and str_contains ( $outT1, 'FAIL  wide' )
              and max ( array_map ( 'strlen', explode ( "\n", $outT1 ) ) ) < 400
              and $codeT2 === 1 and str_contains ( $outT2, 'made  fresh' )
              and trim ( (string) @file_get_contents ( "$tests/fresh.txt" ) ) == 'fresh'
              and $codeT3 === 0 and str_contains ( $outT3, '4 tests, 0 failed' )
              and $codeT4 === 0 and str_contains ( $outT4, '3 tests, 0 failed' )
              and $codeT5 === 1
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

    // --pretend runs nothing, --fresh included: refused before the database is reached, the
    // seeded notes still there - the tables were dropped for real and the run ended on a PHP
    // error

    $notes = function () use ( $home ) {
      try { return (int) ( new PDO ( "sqlite:$home/DATA/shop.sqlite" ) ) -> query ( 'select count(*) from notes' ) -> fetchColumn (); }
      catch ( Throwable $e ) { return -1; }
    };

    $notesBefore = $notes ();

    list ( $codeM9, , $errM9 ) = cliCheckRun ( [ 'migrate', 'shop', '--fresh', '--force', '--pretend' ], $env );

    $notesAfter = $notes ();

    list ( $codeM8, $outM8 ) = cliCheckRun ( [ 'migrate', 'shop', '--fresh', '--force' ], $env );

    $migrate = ( $codeM1 === 0 and preg_match ( '#^apps/shop/_migrations/\d{4}_\d{2}_\d{2}_\d{6}_create_notes\.sql$#', trim ( $outM1 ) )
                 and $codeM2 === 0 and str_contains ( $outM2, 'create table notes (id integer primary key, text varchar(20));' )
                 and $codeM3 === 0 and str_contains ( $outM3, '_create_notes' ) and str_contains ( $outM3, 'seeded      01_notes' )
                 and $codeM4 === 0 and preg_match ( '/yes  1      \S+_create_notes/', $outM4 )
                 and $codeM6 === 1
                 and $codeM7 === 1
                 and $codeM9 === 1 and str_contains ( $errM9, '--pretend cannot show a fresh run' )
                 and $notesBefore > 0 and $notesAfter === $notesBefore
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

    // export of a scratch application with an {img} and a {video} poster: the thumbnails
    // of www/<app>/_thumbs/ go along with the copy, src and srcset both pointing into it, and
    // the poster is the relative path to the picture's copy - the srcset kept the server's
    // /gallery/_thumbs/ addresses, of a directory the copy did not have

    list ( $codeG1 ) = cliCheckRun ( [ 'new', 'gallery' ], $env );

    mkdir ( "$home/www/gallery/photos", 0755, TRUE );
    imagepng ( imagecreatetruecolor ( 400, 200 ), "$home/www/gallery/photos/p.png" );
    file_put_contents ( "$home/www/gallery/clip.mp4", '' );
    file_put_contents ( "$home/apps/gallery/pic.pad",
      "<p>{img 'photos/p.png', width=100, height=50, alt='p'}</p>\n{video 'clip.mp4', title='Clip', poster='photos/p.png'}" );

    list ( $codeG2 ) = cliCheckRun ( [ 'export', 'gallery', "$home/gallery-copy" ], $env );

    $pic = (string) @file_get_contents ( "$home/gallery-copy/pic.html" );

    $exportImg = ( $codeG1 === 0 and $codeG2 === 0
                   and preg_match ( '#src="(_thumbs/[0-9a-f]{16}\.png)" srcset="\1 1x, (_thumbs/[0-9a-f]{16}\.png) 2x"#', $pic, $thumbs )
                   and is_file ( "$home/gallery-copy/{$thumbs [1]}" ) and is_file ( "$home/gallery-copy/{$thumbs [2]}" )
                   and preg_match ( '#poster="photos/p\.png\?v=[0-9a-f]+"#', $pic ) );

    padDeleteDataDir ( $home );

    // pack - what it refuses before node is asked: an application that is not there, a
    // panel it does not know, an option it does not know

    list ( $codeP1, , $errP1 ) = cliCheckRun ( [ 'pack', 'no/such/app' ] );
    list ( $codeP2, , $errP2 ) = cliCheckRun ( [ 'pack', 'demo', '--panel=side' ] );
    list ( $codeP3, , $errP3 ) = cliCheckRun ( [ 'pack', 'demo', '--color=red' ] );

    $pack = ( $codeP1 === 1 and str_contains ( $errP1, "there is no application named 'no/such/app'" )
              and $codeP2 === 1 and str_contains ( $errP2, "--panel is button, key or none, not 'side'" )
              and $codeP3 === 1 and str_contains ( $errP3, 'pad pack does not know --color=red' ) ) ? 'yes' : 'NO';

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
                and substr_count ( $indexHost, 'href="about.html"' ) == 2
                and $exportImg ) ? 'yes' : 'NO';

    padDeleteDataDir ( $dirHost );

  }

?>
