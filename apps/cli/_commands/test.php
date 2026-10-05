<?php

  // pad test <app> [name] [--record]: the application's own tests. A test is a page in the
  // application's _tests/ directory - cart.pad, with cart.php for its data when it needs
  // any - next to its answer, cart.txt, in the three forms the framework's suites use:
  //
  //   an exact body                  the page's output, trimmed, and a 2xx status
  //   /a pattern/                    matched over the trimmed output, and a 2xx status
  //   HTTP 404, then optionally      the status the page ended with, and the pattern over
  //   /a pattern/ on the next line   the output when one is given
  //
  // Each test page is rendered bare - without the application's wrappers - in a child
  // process of its own (pad render with PAD_TEST), with every {assert} in it, and in the
  // pages it brings in, checked: a false one fails the test. A test without an answer
  // counts against the run; --record writes the missing answers from what the pages
  // render now, and never overwrites one. [name] runs one test, or the tests of one
  // directory of _tests/.
  //
  // pad test --all runs the tests of every application that has a _tests/ directory;
  // --brief prints the failures and one summary line - what ci.sh shows. Exit status 1
  // when a test failed or has no answer.

  $testArgs   = array_slice ( $argv, 2 );
  $testRecord = in_array ( '--record', $testArgs );
  $testBrief  = in_array ( '--brief',  $testArgs );
  $testAll    = in_array ( '--all',    $testArgs );
  $testWords  = array_values ( array_filter ( $testArgs, fn ( $one ) => ! str_starts_with ( $one, '--' ) ) );

  if ( $testAll )
    $testApps = testApps ();
  else {

    $testApp = trim ( $testWords [0] ?? '', '/' );

    if ( ! cliApp ( $testApp ) )
      return cliFail ( "there is no application named '$testApp' - pad test <app> [name], or pad test --all" );

    if ( ! is_dir ( cliHome () . "/apps/$testApp/_tests" ) )
      return cliFail ( "$testApp has no tests - they go in apps/$testApp/_tests/: name.pad (and name.php), name.txt the answer" );

    $testApps = [ $testApp ];

  }

  $testOnly  = $testAll ? '' : trim ( $testWords [1] ?? '', '/' );
  $testTotal = $testFailed = $testNew = 0;

  foreach ( $testApps as $testApp ) {

    $testNames = array_values ( array_filter ( testNames ( $testApp ),
                   fn ( $one ) => $testOnly === '' or $one === $testOnly or str_starts_with ( $one, "$testOnly/" ) ) );

    $testPages  = array_map ( fn ( $one ) => "_tests/$one", $testNames );
    $testResult = cliRunPages ( $testApp, $testPages, [ 'PAD_TEST' => '1' ] );
    $testLines  = [];
    $testBad    = 0;
    $testFresh  = 0;

    foreach ( $testNames as $testName ) {

      list ( $testCode, $testOut, $testErr ) = $testResult ["_tests/$testName"];

      $testStatus = preg_match ( '/PAD-STATUS (\d+)/', $testErr, $m ) ? $m [1] : ( $testCode === 0 ? '200' : '500' );
      $testBody   = trim ( $testOut );
      $testFile   = cliHome () . "/apps/$testApp/_tests/$testName.txt";

      $testTotal++;

      if ( ! file_exists ( $testFile ) ) {

        if ( $testRecord ) {
          file_put_contents ( $testFile, str_starts_with ( $testStatus, '2' ) ? $testBody : "HTTP $testStatus" );
          $testLines [] = "  made  $testName - its answer recorded in _tests/$testName.txt";
          continue;
        }

        $testNew++;
        $testFresh++;
        $testLines [] = "  NEW   $testName - no answer yet: pad test $testApp --record writes it from what it renders now";
        continue;

      }

      $testWant = trim ( file_get_contents ( $testFile ) );

      if ( testCompare ( $testWant, $testStatus, $testBody ) ) {
        if ( ! $testBrief )
          $testLines [] = "  ok    $testName";
        continue;
      }

      $testFailed++;
      $testBad++;

      $testJson = json_decode ( $testBody, TRUE );
      $testGot  = "HTTP $testStatus";

      if ( is_array ( $testJson ) and isset ( $testJson ['error'] ) ) {
        $testGot .= ' - ' . preg_replace ( '/^PAD: /', '', $testJson ['error'] );
        if ( isset ( $testJson ['template'] ['file'] ) )
          $testGot .= "\n              " . $testJson ['template'] ['file'] . ':' . $testJson ['template'] ['line'] . ':' . $testJson ['template'] ['column']
                    . '  ' . $testJson ['template'] ['tag'];
      } elseif ( $testBody !== '' )
        $testGot .= "\n              " . str_replace ( "\n", "\n              ", testShort ( $testBody ) );

      $testLines [] = "  FAIL  $testName";
      $testLines [] = "        want: " . str_replace ( "\n", "\n              ", testShort ( $testWant ) );
      $testLines [] = "        got:  $testGot";

    }

    if ( $testBrief and ! $testBad and ! $testFresh )
      continue;

    cliOut ( $testApp );

    foreach ( $testLines as $testLine )
      cliOut ( $testLine );

    if ( ! $testBrief )
      cliOut ( "  " . count ( $testNames ) . " tests, $testBad failed" . ( $testFresh ? ", $testFresh without an answer" : '' ) );

  }

  cliOut ( ( $testAll ? count ( $testApps ) . ' applications, ' : '' )
         . "$testTotal tests, $testFailed failed" . ( $testNew ? ", $testNew without an answer" : '' ) );

  return ( $testFailed or $testNew ) ? 1 : 0;


  // The tests of an application: the pages under its _tests/, less the _ names in there.

  function testNames ( $app ) {

    $names = [];

    foreach ( cliPagesWalk ( cliHome () . "/apps/$app/_tests/", '', "$app/_tests" ) as $page )
      $names [] = $page;

    return $names;

  }

  // Every application with a _tests/ directory: an application is a directory under apps/
  // with an entry point in www/.

  function testApps () {

    $apps = [];
    $home = cliHome ();

    $walk = function ( $dir, $prefix ) use ( &$walk, &$apps, $home ) {

      foreach ( scandir ( $dir ) as $one ) {

        if ( str_starts_with ( $one, '_' ) or str_starts_with ( $one, '.' ) or ! is_dir ( "$dir/$one" ) )
          continue;

        if ( file_exists ( "$home/www/$prefix$one/index.php" ) and is_dir ( "$dir/$one/_tests" ) )
          $apps [] = "$prefix$one";

        $walk ( "$dir/$one", "$prefix$one/" );

      }

    };

    $walk ( "$home/apps", '' );

    sort ( $apps );

    return $apps;

  }

  // The three answer forms, judged the way the suites judge them.

  function testCompare ( $want, $status, $body ) {

    if ( str_starts_with ( $want, 'HTTP ' ) ) {

      $lines   = explode ( "\n", $want, 2 );
      $pattern = trim ( $lines [1] ?? '' );

      return trim ( substr ( $lines [0], 5 ) ) === (string) $status
             and ( $pattern === '' or @preg_match ( $pattern, $body ) );

    }

    if ( ! str_starts_with ( (string) $status, '2' ) )
      return FALSE;

    if ( strlen ( $want ) > 1 and $want [0] == '/' and str_ends_with ( $want, '/' ) )
      return (bool) @preg_match ( $want, $body );

    return $want === $body;

  }

  function testShort ( $text ) {

    $lines = explode ( "\n", $text );

    return implode ( "\n", array_slice ( $lines, 0, 6 ) ) . ( count ( $lines ) > 6 ? "\n..." : '' );

  }

?>
