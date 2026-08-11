<?php

  // Every suite the application runs, one entry each - the single place a suite exists.
  // The key names the suite everywhere: the overview page ?<key>/index, the result file
  // DATA/suites/<key>.json, the ci.sh line, and for the covering suites the store
  // directory apps/regression/<key>/ as well.
  //
  //   walk   the suite application whose pages carry their answers beside them - the
  //          handwritten suites, where an answer encodes intent
  //   over   a covering suite: the applications whose pages it fetches - 'family' for the
  //          regression family, a list of names, or 'other' for every application without
  //          a suite of its own. Answers live in the store, and strip is what the store
  //          takes off the front of app/item to name a test
  //   solo   fetched one request at a time - the self-testers prove their subsystem by
  //          fetching their own probe from inside the request, and a concurrent fetch
  //          could land between an index's two probes and turn a working backend into a NO
  //
  // Framework has neither walk nor over: its cases are one-line engine asserts with a
  // whitespace rule of their own, and getFrameworkRun() keeps them.

  function getSuites () {

    return [

      'pages'      => [ 'walk' => 'regression/pages'  ],
      'common'     => [ 'walk' => 'regression/common' ],
      'errors'     => [ 'walk' => 'regression/errors' ],
      'framework'  => [ ],
      'regression' => [ 'over' => 'family',       'store' => 'regression/regression', 'strip' => 'regression/', 'solo' => TRUE ],
      'sequence'   => [ 'over' => [ 'sequence' ], 'store' => 'regression/sequence',   'strip' => 'sequence/' ],
      'manual'     => [ 'over' => [ 'manual'   ], 'store' => 'regression/manual',     'strip' => 'manual/'   ],
      'other'      => [ 'over' => 'other',        'store' => 'regression/other',      'strip' => ''          ],

    ];

  }


  // Runs every suite, in registry order. Asked for over HTTP from anywhere but this
  // application - the walks and the stores are APP-relative, so a suite runs where it
  // lives; develop's build page reaches the same machinery through ?build&go=1.

  function getRegression ( ) {

    global $padHost, $ciRun;

    $token = isset ( $ciRun ) ? '&ciRun=' . padMakeSafe ( $ciRun, 16 ) : '';

    foreach ( array_keys ( getSuites () ) as $suite )

      if ( APP != APPS . 'regression/main/' )
        padCurl ( $padHost . "regression/main/?$suite/index&test$token" );
      else
        getSuiteTest ( $suite );

  }


  // What a covering suite covers. The family is every application under regression/ that
  // is an application - padAppsListRoot() says so - except the walk suites, whose pages
  // carry their own answers, and framework; the registry says which those are, so a new
  // walk suite excludes itself. The stores keep no pages and no entry point, so the root
  // test keeps them out. 'other' is the complement: every application no covering suite
  // names and no walk holds.

  function getSuiteOverApps ( $over ) {

    if ( is_array ( $over ) )
      return $over;

    if ( $over == 'family' ) {

      $walks = [ 'regression/framework' ];

      foreach ( getSuites () as $entry )
        if ( isset ( $entry ['walk'] ) )
          $walks [] = $entry ['walk'];

      $apps = [];

      foreach ( padFiles ( APPS . 'regression/' ) as $one )
        if ( ! str_starts_with ( $one, '_' )
             and padAppsListRoot ( "regression/$one" )
             and ! in_array ( "regression/$one", $walks ) )
          $apps [] = "regression/$one";

      return $apps;

    }

    $named = [];

    foreach ( getSuites () as $entry )
      if ( is_array ( $entry ['over'] ?? '' ) )
        $named = array_merge ( $named, $entry ['over'] );

    $apps = [];

    foreach ( padAppsList () as $one )
      if ( ! str_starts_with ( $one ['app'], 'regression/' ) and ! in_array ( $one ['app'], $named ) )
        $apps [ $one ['app'] ] = $one ['app'];

    return array_values ( $apps );

  }


  // A covering suite's run: every page of the covered applications, compared against the
  // prediction of the same name in the store. A new application covered by 'other' lands
  // here as 'new' until its predictions are written.

  function getSuiteOverRun ( $suite ) {

    $entry   = getSuites () [$suite];
    $covered = getSuiteOverApps ( $entry ['over'] );
    $work    = [];

    foreach ( padAppsList () as $one ) {

      if ( ! in_array ( $one ['app'], $covered ) )
        continue;

      $name = substr ( $one ['app'] . '/' . $one ['item'], strlen ( $entry ['strip'] ) );

      $work [] = [
        'app'  => $one ['app'],
        'item' => $one ['item'],
        'name' => $name,
        'url'  => getSuiteUrl ( $one ['app'], $one ['item'] ),
        'want' => APPS . $entry ['store'] . '/' . $name . '.txt',
      ];

    }

    $tests   = [];
    $fetched = getSuiteFetch ( $work, $entry ['solo'] ?? FALSE );

    foreach ( $work as $i => $one ) {

      // The work list drives the loop, not the fetch map: a request the multi never
      // answered must come back FAILED, not vanish from the count.

      $curl = $fetched [$i] ?? [ 'data' => '', 'result' => '999' ];

      $test = getSuiteOne ( $one ['app'], $one ['item'], $one ['url'], $one ['want'], $curl );

      $test ['name'] = $one ['name'];
      $test ['what'] = getSuiteWhatList () [ $one ['app'] . '/' . $one ['item'] ] ?? '';

      $tests [] = $test;

    }

    return getSuiteResult ( $tests );

  }


  // The fetches for a run: windows of independent GETs through padCurlMulti for a suite
  // whose pages are stateless - the crawl always fetched them twelve-wide - and one
  // request at a time where the registry says solo.

  function getSuiteFetch ( $work, $solo ) {

    $fetched = [];

    if ( $solo ) {

      foreach ( $work as $i => $one ) {

        set_time_limit ( 60 );

        $fetched [$i] = padCurl ( $one ['url'] );

      }

      return $fetched;

    }

    foreach ( array_chunk ( $work, 48, TRUE ) as $chunk ) {

      set_time_limit ( 60 );

      $urls = [];

      foreach ( $chunk as $i => $one )
        $urls [$i] = $one ['url'];

      foreach ( padCurlMulti ( $urls ) as $i => $curl )
        $fetched [$i] = $curl;

    }

    return $fetched;

  }


  // The runner behind pages/ and common/, the second kind of regression test in this
  // application.
  //
  // A pages test is an ordinary page - name.pad, name.php, or the pair - fetched over HTTP
  // exactly as a browser would, with &padInclude so it renders bare, without the menu and title
  // the wrapper puts round it. What comes back is compared with name.txt, written beside it.
  //
  // The tests live in two applications, one suite each, and which application a page is in is
  // itself the assertion: regression/pages - the Pages suite, driven from pages/ - has _common
  // switched off, so its pages prove they need nothing but their own application, and
  // regression/common - the Common suite, driven from common/ - holds the pages that use _common:
  // {example}, {demo}, {table}, the {block} snippet, the colouring functions in _common/_lib/.
  //
  // A test can also be a whole directory - see getSuitePages() for when an index stands for the
  // files beside it and when it does not.
  //
  // name.txt is written by hand and nothing here ever rewrites it. That is the whole point: an
  // expectation that the harness records for itself is not a prediction, it is a copy of
  // whatever the code did, and a change to it would be recorded rather than reported. A test
  // with no name.txt yet comes up 'new' and the overview shows exactly what came back, which is
  // what goes in the file.

  function getSuiteDir ( $app ) {

    return APPS . "$app/";

  }


  // Every test under pages/, walked to the bottom. Either half names a test, so the .pad-only and
  // .php-only forms both count and a pair counts once. An underscore hides a directory or a file
  // from the walk, the way it does everywhere else in PAD.
  //
  // The rule for a directory holding an index: if that index renders its siblings - if its source
  // has a {page of them - then it is the only test in the directory, because running it has
  // already run them. A {page} chain of nine is one answer, not nine, and eight of those files
  // assert a fragment nobody asks for on its own.
  //
  // An index that only links to its siblings collapses nothing. error/index is a menu of ten
  // error pages and each of those is its own test; deep/index renders deep/two, which renders
  // three, which renders four, and is one.
  //
  // A root index is a test like any other, and the one test of its application that is fetched
  // without &padInclude - see getSuiteUrl() - so what it asserts is the frame itself: the
  // _common wrapper, the title its _inits.php derives, the tidied whole. Neither application
  // is obliged to have one; regression/pages does not, since its frame is no frame at all.

  function getSuitePages ( $app ) {

    return getSuitePagesWalk ( getSuiteDir ( $app ), '' );

  }


  function getSuiteRenders ( $dir ) {

    foreach ( [ 'pad', 'php' ] as $half ) {

      $source = padFileGet ( "{$dir}index.$half" );

      if ( str_contains ( $source, '{page' ) )
        return TRUE;

    }

    return FALSE;

  }


  function getSuitePagesWalk ( $dir, $prefix ) {

    $list = $names = $dirs = [];

    if ( ! is_dir ( $dir ) )
      return $list;

    foreach ( padFiles ( $dir ) as $file ) {

      if ( str_starts_with ( $file, '_' ) )
        continue;

      if ( is_dir ( "$dir$file" ) )
        $dirs [] = $file;

      elseif ( str_ends_with ( $file, '.pad' ) or str_ends_with ( $file, '.php' ) )
        $names [ substr ( $file, 0, -4 ) ] = TRUE;

      elseif ( str_ends_with ( $file, '.html' ) )
        $names [ substr ( $file, 0, -5 ) ] = TRUE;

    }

    // The same action rule the shared walker applies: a page with no template that
    // redirects, restarts or writes is a fixture for a scenario, not a test - the request
    // group's hop fixture is exactly that.

    foreach ( array_keys ( $names ) as $name )
      if ( ! file_exists ( "$dir$name.pad" ) and ! file_exists ( "$dir$name.html" )
           and file_exists ( "$dir$name.php" ) ) {

        $source = padFileGet ( "$dir$name.php" );

        if ( str_contains ( $source, 'padRedirect'      )
          or str_contains ( $source, 'padRestart'       )
          or str_contains ( $source, 'padFilePut'       )
          or str_contains ( $source, 'padDeleteDataDir' ) )
          unset ( $names [$name] );

      }

    if ( $prefix and isset ( $names ['index'] ) and getSuiteRenders ( $dir ) )
      $names = [ 'index' => TRUE ];

    foreach ( array_keys ( $names ) as $name )
      $list [] = $prefix . $name;

    foreach ( $dirs as $one )
      $list = array_merge ( $list, getSuitePagesWalk ( "$dir$one/", "$prefix$one/" ) );

    sort ( $list );

    return $list;

  }


  // The url a test is fetched from. $padHost carries the mount prefix, so this is
  // http://host/pad/regression/pages/?name&padInclude here and the right thing anywhere else. The
  // tests are applications of their own - regression/pages with _common switched off, regression/common
  // for the pages that use it - and the overview that drives them stays in regression.
  //
  // A root index renders full, the same rule the crawl applies: everything else asserts a bare
  // page, that one asserts the frame around it.

  function getSuiteUrl ( $app, $name, $extra='' ) {

    global $padHost;

    $include = ( $name != 'index' ) ? '&padInclude' : '';

    return $padHost . "$app/?$name$include$extra";

  }


  function getSuiteWantFile ( $app, $name ) {

    return getSuiteDir ( $app ) . "$name.txt";

  }


  // What each test is for. Kept here rather than in the pages themselves, because a page is
  // fetched and compared byte for byte: a comment added to one to describe it changes what it
  // renders, and several of these are sensitive enough to a stray line break that writing the
  // description into them broke twelve of the eighty-five.
  //
  // A name with no entry shows an empty cell rather than an error, so adding a test does not mean
  // editing this first.

  // Looked up under app/name first, then under the name alone: the names rarely collide
  // across the two applications, so most entries stay short, and the one that does - each
  // suite has an error/index now - says which it means.

  function getSuiteWhat ( $app, $name ) {

    $list = getSuiteWhatList ();

    return $list ["$app/$name"] ?? $list [$name] ?? '';

  }


  // Fetches every test and compares. The comparison is exact apart from the whitespace at the two
  // ends, which is what a template's own line breaks leave and says nothing about the page.
  //
  // Two expectations are not a body. A file opening with HTTP <code> asserts the response code,
  // because the error dump carries a request id and absolute paths, so a page that exists to
  // fail can never match byte for byte. The code alone proves only that something failed, so
  // a second line holding /a regular expression/ asserts that the right thing did. A file
  // with slashes at both ends is a regular expression over the whole body, for a page that
  // draws a different answer each time.

  // A page is one comparison, but not always one assertion: the catalog/ pages state one
  // assertion per labelled line, so for those the answer's "name: ..." lines are counted as
  // tests. Every other page - and a catalogue answer that is a code, a pattern, or one
  // rendering - counts one test however long its answer, because its lines are not a list.

  function getSuiteCount ( $name, $expect ) {

    if ( ! str_starts_with ( $name, 'catalog/' ) )
      return 1;

    return max ( 1, preg_match_all ( '/^\S+: /m', $expect ) );

  }


  function getSuiteWalkRun ( $app ) {

    $work = [];

    foreach ( getSuitePages ( $app ) as $name )
      $work [] = [
        'name' => $name,
        'url'  => getSuiteUrl      ( $app, $name ),
        'want' => getSuiteWantFile ( $app, $name ),
      ];

    $tests   = [];
    $fetched = getSuiteFetch ( $work, FALSE );

    foreach ( $work as $i => $one )
      $tests [] = getSuiteOne ( $app, $one ['name'], $one ['url'], $one ['want'],
                                $fetched [$i] ?? [ 'data' => '', 'result' => '999' ] );

    return getSuiteResult ( $tests );

  }


  // One test: fetch the url, compare with the answer file, return the row the overviews
  // render. The caller says where both live, which is what lets the Regression suite hold
  // its predictions apart from its pages.

  function getSuiteOne ( $app, $name, $url, $want, $fetched=NULL ) {

    $curl = $fetched ?? padCurl ( $url );

    $got  = trim ( $curl ['data'] );
    $code = $curl ['result'];

    if ( ! file_exists ( $want ) ) {

      $status = 'new';
      $expect = '';

    } else {

      $expect = trim ( padFileGet ( $want ) );

      list ( $ok, $got ) = getSuiteCompare ( $expect, $got, $code, trim ( $curl ['data'] ) );

      $status = $ok ? 'ok' : 'FAILED';

    }

    // A passing row stores no bodies: nothing renders want or got for it, and keeping
    // them put four megabytes of pages nobody read into one suite's result file.

    // padEscape() as well: want and got render inside the overview templates, and a body
    // full of braces - a JSON dump, a pattern - would otherwise be parsed as PAD markup
    // and break the overview page exactly when a test fails.

    return [
      'name'   => $name,
      'url'    => $url,
      'what'   => getSuiteWhat ( $app, $name ),
      'want'   => ( $status == 'ok' ) ? '' : padEscape ( htmlspecialchars ( $expect ) ),
      'got'    => ( $status == 'ok' ) ? '' : padEscape ( htmlspecialchars ( $got    ) ),
      'status' => $status,
      'failed' => ( $status == 'FAILED' ) ? 1 : 0,
      'count'  => getSuiteCount ( $name, $expect )
    ];

  }


  // The three answer forms, judged in one place for every suite: an HTTP code with an
  // optional pattern over the raw body, a /pattern/ over the trimmed body, or an exact
  // body - the last two insisting on a healthy response, because a fragment surviving
  // inside an error dump must not pass a test that meant to assert a working page.
  // Returns the verdict and what got should say about it.

  function getSuiteCompare ( $expect, $got, $code, $body ) {

    if ( str_starts_with ( $expect, 'HTTP ' ) ) {

      $expectLines   = padExplode ( $expect, "\n" );
      $expectCode    = trim ( substr ( $expectLines [0], 5 ) );
      $expectPattern = $expectLines [1] ?? '';

      $ok  = ( $expectCode === (string) $code );
      $got = "HTTP $code";

      if ( $ok and $expectPattern ) {

        $ok = (bool) preg_match ( $expectPattern, trim ( $body ) );

        $got = ( $ok ) ? "HTTP $code, matches $expectPattern"
                       : "HTTP $code\n" . trim ( $body );

      }

      return [ $ok, $got ];

    }

    if ( strlen ( $expect ) > 1 and str_starts_with ( $expect, '/' ) and str_ends_with ( $expect, '/' ) ) {

      // The parentheses matter: 'and' binds after '=', and without them the health check
      // was silently discarded - a pattern answer passed over any status code from the
      // day this was written, and the harness fault-injection case is what caught it.

      $ok = ( (bool) preg_match ( $expect, $got ) and str_starts_with ( (string) $code, '2' ) );

      if ( $ok )
        $got = "matches $expect";

      return [ $ok, $got ];

    }

    $ok = ( $got === $expect and str_starts_with ( (string) $code, '2' ) );

    return [ $ok, $got ];

  }


  // A test with no recorded answer is not passing - it is waiting for one. It has its own
  // count rather than a place in 'failed', so the overview can say which it is, and ci.sh
  // gates on both.

  function getSuiteResult ( $tests ) {

    $count  = 0;
    $failed = 0;
    $new    = 0;

    foreach ( $tests as $test ) {

      $count  += $test ['count'];
      $failed += $test ['failed'];

      if ( $test ['status'] == 'new' )
        $new++;

    }

    return [
      'tests'   => $tests,
      'summary' => count ( $tests ) . " pages, $count tests, $failed failed" . ( $new ? ", $new new" : '' ),
      'failed'  => $failed,
      'new'     => $new,
      'when'    => time ()
    ];

  }


  // Where a run is kept - one file per suite, pages.json, common.json and framework.json.
  // DATA is the writable tree and is not in git, which is what a result regenerated on
  // demand should be; the crawler's own baselines live beside it under regression/, so the
  // suites get a directory of their own rather than sharing that one.

  function getSuiteFile ( $suite ) {

    return DATA . "suites/$suite.json";

  }


  // Runs one suite and keeps the result. This is what the Test link on a suite page asks for,
  // and the only thing that runs a suite at all.

  function getSuiteTest ( $suite ) {

    set_time_limit ( 60 );

    $entry = getSuites () [$suite];

    $result = isset ( $entry ['walk'] ) ? getSuiteWalkRun     ( $entry ['walk'] )
            : ( isset ( $entry ['over'] ) ? getSuiteOverRun ( $suite )
            : getFrameworkRun () );

    // The run token ties a result to the trigger that asked for it - ci.sh passes one and
    // refuses results that are not its own - and the commit says what was tested.

    $result ['run']    = padMakeSafe ( $GLOBALS ['ciRun'] ?? '', 16 );
    $result ['commit'] = getSuiteCommit ();

    padFilePut ( getSuiteFile ( $suite ), json_encode ( $result ) );

    return $result;

  }


  // Every page owned by exactly one suite - the invariant the registry promises, checked
  // independently: a walk suite owns its application's pages outright, framework owns
  // exactly what its enumerator fetches, a covering suite owns its covered applications.
  // What nothing owns, or two things own, is named - a page like the framework root index
  // once was, alive with no suite behind it, cannot slip through again.

  function getSuiteParity () {

    $walks = [];
    $overs = [];

    foreach ( getSuites () as $suite => $entry )
      if     ( isset ( $entry ['walk'] ) ) $walks [ $entry ['walk'] ] = $suite;
      elseif ( isset ( $entry ['over'] ) ) $overs [ $suite ] = getSuiteOverApps ( $entry ['over'] );

    $framework = [];

    foreach ( getFrameworkList () as $name )
      $framework [ "regression/framework/$name" ] = TRUE;

    $wrong = [];

    foreach ( padAppsList () as $one ) {

      $page   = $one ['app'] . '/' . $one ['item'];
      $owners = isset ( $walks [ $one ['app'] ] ) ? 1 : 0;

      if ( $one ['app'] == 'regression/framework' and isset ( $framework [$page] ) )
        $owners++;

      foreach ( $overs as $covered )
        if ( in_array ( $one ['app'], $covered ) )
          $owners++;

      if ( $owners != 1 )
        $wrong [] = "$page ($owners)";

    }

    return $wrong ? implode ( ', ', $wrong ) : 'every page owned once';

  }


  // Every answer file holds exactly one well-formed oracle: an exact body (anything), a
  // /pattern/ that compiles, or an HTTP form of at most two lines whose optional second
  // line is a compiling pattern - a third line is dead weight the comparison would never
  // read, so it is named here instead of silently ignored. Underscore-prefixed files are
  // fixtures, not answers.

  function getSuiteLint () {

    $roots = [];

    foreach ( getSuites () as $entry )
      if     ( isset ( $entry ['walk']  ) ) $roots [] = APPS . $entry ['walk'];
      elseif ( isset ( $entry ['store'] ) ) $roots [] = APPS . $entry ['store'];

    $roots [] = APPS . 'regression/framework';

    $bad = [];

    foreach ( $roots as $root ) {

      $directory = new RecursiveDirectoryIterator ( $root );
      $iterator  = new RecursiveIteratorIterator  ( $directory );

      foreach ( $iterator as $one ) {

        $path = padCorrectPath ( $one->getPathname () );

        if ( ! str_ends_with ( $path, '.txt' ) or str_starts_with ( basename ( $path ), '_' ) )
          continue;

        $name = str_replace ( APPS, '', $path );
        $want = trim ( padFileGet ( $path ) );

        if ( str_starts_with ( $want, 'HTTP ' ) ) {

          $lines = padExplode ( $want, "\n" );

          if ( count ( $lines ) > 2 )
            $bad [] = "$name has a third line nothing reads";
          elseif ( isset ( $lines [1] ) and @preg_match ( $lines [1], '' ) === FALSE )
            $bad [] = "$name has a pattern line that does not compile";

        }

        elseif ( strlen ( $want ) > 1 and str_starts_with ( $want, '/' ) and str_ends_with ( $want, '/' ) ) {

          if ( @preg_match ( $want, '' ) === FALSE )
            $bad [] = "$name does not compile";

        }

      }

    }

    return $bad ? implode ( ', ', $bad ) : 'every answer well formed';

  }


  // The commit the tree stood on when a result was written, so a verdict says what it is
  // a verdict about. Empty where git or shell access is absent; the gate holds the
  // binding only when both sides know their commit.

  function getSuiteCommit () {

    static $commit = NULL;

    return $commit ??= trim ( (string) @shell_exec (
      'git -C ' . escapeshellarg ( dirname ( APPS ) ) . ' rev-parse --short HEAD 2>/dev/null' ) );

  }


  // One verdict for every reader, worst state first: a suite that never ran is not ok,
  // and neither is one waiting on a new test - ci.sh gates on both, and what a person
  // reads must say what the gate would.

  function getSuiteVerdict ( $result ) {

    if ( ! ( $result ['when'] ?? 0 )   ) return 'NEVER RUN';
    if (   $result ['failed']          ) return 'FAILURES';
    if (   $result ['new'] ?? 0        ) return 'NEW';

    return 'all ok';

  }


  // What a page load reads: the last run, without starting a new one - a missing or
  // unreadable result answers 'never run', and Test is the only thing that runs. Fetching
  // every test is a request each, so opening a report must never cost a suite.

  function getSuiteLast ( $suite ) {

    $file = getSuiteFile ( $suite );

    if ( file_exists ( $file ) ) {

      $result = json_decode ( padFileGet ( $file ), TRUE );

      if ( is_array ( $result ) and isset ( $result ['tests'] ) )
        return $result;

    }

    return [ 'tests' => [], 'summary' => 'never run', 'failed' => 0, 'new' => 0, 'when' => 0 ];

  }


  // The Framework suite: every engine case as a fetched page, exactly the Pages model.
  // Every case is a triple under apps/regression/framework/<group>/ - the .pad is the template, the
  // .txt the outcome beside it, an optional .php the variables - and each is fetched
  // directly as the page it is, so a case gets the isolation a request brings and the
  // crawl walks it like any other page.
  //
  // A case is written one statement to a line, so every line break and the indentation
  // after it come out of the body before it meets the outcome - and the two ends are
  // trimmed, which is what padCurl does to a body anyway. A .txt with slashes at both
  // ends is a regular expression, as everywhere else.

  function getFrameworkDir () {

    return APPS . 'regression/framework/';

  }


  function getFrameworkList () {

    $list = [];

    foreach ( padFiles ( getFrameworkDir () ) as $group ) {

      if ( str_starts_with ( $group, '_' ) )
        continue;

      if ( ! is_dir ( getFrameworkDir () . $group ) )
        continue;

      foreach ( padFiles ( getFrameworkDir () . $group ) as $file )
        if ( str_ends_with ( $file, '.pad' ) )
          $list [] = "$group/" . substr ( $file, 0, -4 );

    }

    sort ( $list );

    return $list;

  }


  function getFrameworkUrl ( $name ) {

    global $padHost;

    return $padHost . "regression/framework/?$name&padInclude";

  }


  function getFrameworkRun () {

    $tests  = [];
    $groups = [];
    $total  = 0;
    $failed = 0;
    $new    = 0;

    // Nine hundred independent GETs, so they go a window at a time through padCurlMulti,
    // and the answers are judged in order as before.

    foreach ( array_chunk ( getFrameworkList (), 48 ) as $chunk ) {

      set_time_limit ( 60 );

      $urls = [];

      foreach ( $chunk as $i => $name )
        $urls [$i] = getFrameworkUrl ( $name );

      $fetched = padCurlMulti ( $urls );

      foreach ( $chunk as $i => $name ) {

        $url  = $urls [$i];
        $want = getFrameworkDir () . "$name.txt";
        $curl = $fetched [$i] ?? [ 'data' => '', 'result' => '999' ];

        $got  = trim ( preg_replace ( '/\n\s*/', '', $curl ['data'] ) );
        $code = $curl ['result'];

        if ( ! file_exists ( $want ) ) {

          $status = 'new';
          $expect = '';

        } else {

          $expect = trim ( padFileGet ( $want ) );

          list ( $ok, $got ) = getSuiteCompare ( $expect, $got, $code, $curl ['data'] );

          $status = $ok ? 'ok' : 'FAILED';

        }

        $total++;
        $groups [ substr ( $name, 0, strpos ( $name, '/' ) ) ] = TRUE;

        if ( $status == 'FAILED' ) $failed++;
        if ( $status == 'new'    ) $new++;

        $tests [] = [
          'name'   => $name,
          'url'    => $url,
          'want'   => ( $status == 'ok' ) ? '' : padEscape ( htmlspecialchars ( $expect ) ),
          'got'    => ( $status == 'ok' ) ? '' : padEscape ( htmlspecialchars ( $got    ) ),
          'status' => $status,
          'failed' => ( $status == 'FAILED' ) ? 1 : 0
        ];

      }

    }

    return [
      'tests'   => $tests,
      'summary' => count ( $groups ) . " groups, $total tests, $failed failed" . ( $new ? ", $new new" : '' ),
      'failed'  => $failed,
      'new'     => $new,
      'when'    => time ()
    ];

  }


  // Everything a suite's overview page needs: run the suite when Test asked, read the
  // last run otherwise, and leave the fields the shared _include templates render. The
  // per-suite pages keep only their prose.

  function getSuitePage ( $suite ) {

    global $test, $padPage, $tests, $summary, $failedCount, $verdict, $title, $suiteKey, $recordable;

    if ( isset ( $test ) ) {

      getSuiteTest ( $suite );

      padRedirect ( $padPage );

    }

    $result = getSuiteLast ( $suite );

    $tests       = $result ['tests'];
    $summary     = $result ['summary'];
    $failedCount = $result ['failed'];
    $verdict     = getSuiteVerdict ( $result );
    $title       = ucfirst ( $suite ) . " suite - $summary";
    $suiteKey    = $suite;
    $recordable  = isset ( getSuites () [$suite] ['over'] ) ? 1 : 0;

  }

?>