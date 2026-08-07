<?php


  // Where the two suites live. They belong to the regression application wherever they are driven
  // from - develop's build page calls getRegression() as well - so the directories are named
  // absolutely rather than against APP.
  //
  // Against APP they resolved to whatever application was running. A build run from develop
  // looked for apps/develop/sandbox/_cases/ and apps/develop/pages/, found neither, and wrote
  // "no cases for 'tags'" over every stored result; the crawl then stored the suite pages saying
  // that, and getRegressionWarning('ok') accepted it as the baseline. getPagesUrl() has always
  // named the application outright, so this only makes the rest of it agree.

  function getRegressionApp () {

    return APPS . 'regression/';

  }


  function getRegression ( $extra='' ) {

    getRegressionSandbox ();
    getRegressionPages   ();
    getRegressionAll     ( $extra );

  }


  function getRegressionAll ( $extra='' ) {

    set_time_limit ( 60 );

    foreach ( padAppsList () as $one ) {

      extract ( $one );

      getRegressionGo ( $app, $item, $extra );

    }

  }


  // The session and request ids a page carries are different on every request by design, so a
  // page that writes them into its own output can never match a stored copy. {ajax} does exactly
  // that - padAddIds() appends padSesID and padReqID to the url it builds - and that alone was
  // enough to keep reference/pages and regression/show/index permanently on warning.
  //
  // They are taken out of both sides before comparing, and only for comparing: what is stored is
  // still the page as it came, so the show page diffs the real thing.

  function getRegressionCompare ( $text, $draw = FALSE ) {

    $text = preg_replace ( '/padSesID=[A-Za-z0-9]+/', 'padSesID=', $text );
    $text = preg_replace ( '/padReqID=[A-Za-z0-9]+/', 'padReqID=', $text );

    // With $draw, every run of digits goes too. That is what lets a page which draws numbers be
    // compared at all: the values it happened to pick are the part that cannot match, and the
    // headings, rows, table structure and everything else around them are the 93% that can.

    // A run of drawn values collapses to one. A page can draw a different *number* of values as
    // well as different values - {mySeq randomly, unique} drops duplicates, so eight terms one
    // run and six the next - and without this the row count alone reported a change that was
    // only ever the draw. A heading, a column or a tag still shows, which is the point.

    if ( $draw ) {
      $text = preg_replace ( '/\d+/',      '#', $text );
      $text = preg_replace ( '/#(\s+#)+/', '#', $text );
    }

    return $text;

  }


  // Whether a page says of itself that it draws. The three words are matched with str_contains
  // rather than strpos, which returns 0 for a word at the very start of a file and reads as false.

  function getRegressionDraws ( $source ) {

    return str_contains ( $source, 'random'  )
        or str_contains ( $source, 'shuffle' )
        or str_contains ( $source, 'chance'  );

  }


  // What to make of a page that draws. It used to be skipped outright, which meant a regression
  // in one of the twenty-seven such pages could not be seen: across them that is 4542 lines of
  // output, of which only 7% actually varies from run to run.
  //
  // So the draw is masked and the rest compared. Where that matches, the page is 'random' as
  // before - the colour still says it cannot be compared exactly - but it now means "looked at
  // and unchanged" instead of "not looked at".
  //
  // Where it does not match, the page is asked for once more. If two fresh draws differ from each
  // other even masked then the shape itself varies per run and there is nothing to compare, so it
  // stays 'random'. Otherwise the page really has changed, and that is a warning.

  function getRegressionDraw ( $url, $old, $new ) {

    if ( getRegressionCompare ( $old, TRUE ) == getRegressionCompare ( $new, TRUE ) )
      return 'random';

    // Asked for once more. Where even two fresh draws differ after masking, the page is one
    // nothing can be concluded about and it stays 'random'; where they agree, the stored copy is
    // the odd one out and something really has changed.

    if ( getRegressionCompare ( $new, TRUE ) != getRegressionCompare ( padCurl ( $url ) ['data'], TRUE ) )
      return 'random';

    return 'warning';

  }


  function getRegressionGo ( $app, $item, $extra='' ) {

    global $padHost;

    $include = ( $item != 'index' ) ? '&padInclude' : '';
    $store   = DATA . "regression/$app/$item.html";

    $curl   = padCurl    ( "$padHost$app/?$item$include$extra" );
    $source = padFileGet ( APPS . "$app/$item.pad" );
    $old    = padFileGet ( $store );

    $good = str_starts_with ( $curl ['result'], '2');
    $new  = $curl ['data'];

    // The crawl's own overview lists the status of every page, and those change as the crawl
    // walks them - so by the time it reaches this one, what it renders no longer matches what
    // was stored a moment before, and never will. Comparing a report against the run producing
    // it is circular; it is marked and left alone.

    if     ( "$app/$item" == 'regression/all' ) $status = 'random';
    elseif ( ! $good                    ) $status = 'error';
    elseif ( ! file_exists ($store)     ) $status = 'new';
    elseif ( ! trim ($new)              ) $status = 'empty';
    elseif ( getRegressionDraws ( $source ) )
                                          $status = getRegressionDraw ( "$padHost$app/?$item$include$extra", $old, $new );
    elseif ( getRegressionCompare ( $old ) == getRegressionCompare ( $new ) ) $status = 'ok';
    else                                  $status = 'warning';

    if ( $status == 'new' )
      padFilePut ( $store, $new ) ;

    padFilePut ( str_replace ( '.html', '.txt', $store ), $status ) ;

    if ( ! $extra != '&padExamples' or ! str_starts_with ( $curl ['result'], '2' ) )
      return;

    if ( str_contains ( $source, '{page'    ) ) return;
    if ( str_contains ( $source, '{example' ) ) return;
    if ( str_contains ( $source, '{ajax'    ) ) return;
    if ( str_contains ( $source, '{table'   ) ) return;
    if ( str_contains ( $source, '{demo'    ) ) return;
 
    if ( file_exists ( APPS . "$app/$item.php" ) )
      padFilePut ( "examples/$app/$item.php",  padFileGet ( APPS . "$app/$item.php" ) );

    padFilePut ( "examples/$app/$item.pad",  padTidySmall ( $source,        TRUE ) );
    padFilePut ( "examples/$app/$item.html", padTidySmall ( $curl ['data'], TRUE ) );

  }


  function padAppsList () {

    $directory = new RecursiveDirectoryIterator (APPS);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname() );

      if ( strpos ( $path, '/_')      ) continue;
      if ( strpos ( $path, 'develop') ) continue;

      $ext = substr($path, strrpos($path, '.')+1 );

      if ( $ext != 'pad' ) 
        continue;

      $file  = str_replace ( APPS, '', $path );

      $app   = substr($file, 0, strpos($file, '/')   );
      $file  = substr($file,    strpos($file, '/')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );

      $files ["$app/$item"] ['path'] = $path;
      $files ["$app/$item"] ['app']  = $app;
      $files ["$app/$item"] ['item'] = $item;

    }

    ksort ($files);

    return $files;

  }


  // The runner behind pages/, the second kind of regression test in this application.
  //
  // A sandbox case is a template string rendered inside the running request. That is quick and
  // self-contained, but it is not a request: a nested pass has its own variable scope, so a page
  // whose .php file leaves something behind for its .pad file cannot be written as one, and
  // neither can anything that reads $GLOBALS or depends on how a request is built.
  //
  // A pages test is an ordinary page of this application - name.pad, name.php, or the pair -
  // fetched over HTTP exactly as a browser would, with &padInclude so it renders bare, without
  // the menu and title the wrapper puts round it. What comes back is compared with name.txt,
  // written beside it.
  //
  // A test can also be a whole directory - see getPagesList() for when an index stands for the
  // files beside it and when it does not.
  //
  // name.txt is written by hand and nothing here ever rewrites it. That is the whole point: an
  // expectation that the harness records for itself is not a prediction, it is a copy of
  // whatever the code did, and a change to it would be recorded rather than reported. A test
  // with no name.txt yet comes up 'new' and the overview shows exactly what came back, which is
  // what goes in the file.

  function getPagesDir () {

    return getRegressionApp () . 'pages/';

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
  // The root index is the overview and never a test.

  function getPagesList () {

    return getPagesWalk ( getPagesDir (), '' );

  }


  function getPagesRenders ( $dir ) {

    foreach ( [ 'pad', 'php' ] as $half ) {

      $source = padFileGet ( "{$dir}index.$half" );

      if ( str_contains ( $source, '{page' ) )
        return TRUE;

    }

    return FALSE;

  }


  function getPagesWalk ( $dir, $prefix ) {

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

    }

    if ( $prefix and isset ( $names ['index'] ) and getPagesRenders ( $dir ) )
      $names = [ 'index' => TRUE ];

    elseif ( ! $prefix )
      unset ( $names ['index'] );

    foreach ( array_keys ( $names ) as $name )
      $list [] = $prefix . $name;

    foreach ( $dirs as $one )
      $list = array_merge ( $list, getPagesWalk ( "$dir$one/", "$prefix$one/" ) );

    sort ( $list );

    return $list;

  }


  // The url a test is fetched from. $padHost carries the mount prefix, so this is
  // http://host/pad/regression/?pages/name&padInclude here and the right thing anywhere else.

  function getPagesUrl ( $name ) {

    global $padHost;

    return $padHost . "regression/?pages/$name&padInclude";

  }


  function getPagesWantFile ( $name ) {

    return getPagesDir () . "$name.txt";

  }


  // What each test is for. Kept here rather than in the pages themselves, because a page is
  // fetched and compared byte for byte: a comment added to one to describe it changes what it
  // renders, and several of these are sensitive enough to a stray line break that writing the
  // description into them broke twelve of the eighty-five. getCasesGroups() describes the sandbox
  // groups the same way, from the same file, for the same reason.
  //
  // A name with no entry shows an empty cell rather than an error, so adding a test does not mean
  // editing this first.

  function getPagesWhat ( $name ) {

    return getPagesWhatList () [$name] ?? '';

  }


  function getPagesWhatList () {

    return [

      'callback'                  => 'The row phase of a streaming callback, which reads the occurrence fields as plain variables',
      'globals'                   => 'object: over a page variable, which in a request is a global like any other',
      'pairing'                   => 'A page pair: the .php half fills in what the .pad half prints',
      'phponly'                   => 'A page with no template at all - what its .php echoes is the page',
      'db/array'                  => 'The db() array command through a page variable, and the else branch when nothing matches',
      'db/array2'                 => 'The same through the {array} tag, which hands its parameter to db() as the command word',
      'db/check'                  => 'The db() check command, which answers whether a row exists rather than returning one',
      'db/check2'                 => 'The same through the {check} tag',
      'db/field'                  => 'The db() field command, which returns one value',
      'db/field2'                 => 'The same through the {field} tag',
      'db/record'                 => 'The db() record command, which returns one row',
      'db/record2'                => 'The same through the {record} tag',
      'deep/index'                => 'A page rendering a page rendering a page, each level with its own _lib and _include',
      'error/index'               => 'The menu of the pages that end the request; each of those is a test of its own',
      'error/error_1'             => 'padError() raised from a tag - the request ends and the response is 500',
      'error/error_2'             => 'padError() raised from the page rather than from a tag',
      'error/exception_1'         => 'An uncaught PHP exception from a tag',
      'error/exception_2'         => 'An uncaught PHP exception from a page',
      'error/pad_1'               => 'The {error} tag, which ends the request from the template',
      'error/pad_2'               => 'The same failure raised from the page',
      'error/shutdown_1'          => 'A fatal error, which the shutdown handler catches rather than the error handler',
      'error/shutdown_2'          => 'The same from a page',
      'error/warning_1'           => 'A PHP warning, which $padErrorLevel promotes to an ended request',
      'error/warning_2'           => 'The same from a page',
      'file/file'                 => 'The {file} tag writing four files, with date, stamp and id naming them',
      'file/done'                 => 'The page a file-output request lands on, reporting the name it was handed',
      'file/index'                => 'Output type file: the page is written to disk and the request restarts on file/done',
      'functions/date'            => 'The date function over a timestamp, a format and a strtotime modifier',
      'hello/index'               => 'The smallest page pair there is, and the one redirect and restart land on',
      'misc/eval'                 => 'The evaluator over the forms the manual shows - precedence, the @ placeholder, pipes',
      'misc/local'                => 'Reading a file from _data/ by name, in each format padData() accepts',
      'misc/parms'                => 'Parameters, options and variables on a tag, over a PHP array, a constant and a store',
      'manual/3_ways_to_make_a_table' => 'The start, end and else constructs over one table',
      'manual/doc3'               => 'Tag properties read through a content store',
      'manual/name'               => 'How PAD names a tag, and the name option that overrides it',
      'manual/table_fun'          => 'Ten ways of showing one table, each embedding one of the tableFun pages',
      'manual/variable_kinds'     => 'The variable prefixes, as far as the manual page got',
      'manual/z99'                => 'A page whose .php returns a value its .pad does not use',
      'select/demo/join'          => 'The select subsystem walking a declared relation - a join written as nesting',
      'select/demo/union'         => 'A select table declared over a union of several tables',
      'select/support/test'       => 'A select table with a relation, read by id',
      'select/support/topic'      => 'A content store merged into a select result through @content@',
      'select/support/json'       => 'A note rather than a test - see _cases/README.md. It does exercise htmlAttrJson',
      'tags/redirect'             => 'The {redirect} tag, and the variable it hands the page it lands on',
      'tags/restart'              => 'The {restart} tag, which re-runs the request on another page carrying a variable',
      'tags/script'               => 'The script: prefix running a shell, python, perl and php script from _scripts/',
      'vars/at/1'                 => 'The @ reference over a flat global and a nested one, by name and by wildcard',
      'vars/at/2'                 => 'The same references where the value is buried five levels deep',
      'vars/at/7'                 => 'The @ reference walking an XML file, with optional hiding what is not there',
      'vars/at/8'                 => 'A dotted path with a condition at each step, into a data file',
      'vars/at/9'                 => 'The @ reference as a tag pair and through the {at} tag, over the same data',
      'vars/at/random'            => 'The wildcard forms of a dotted reference, each drawn fresh, asserted by shape',
      'start/index'               => 'Everything under start/ at once - the code, page and combi chains, nested and hello',
      'start/hello'               => 'One page rendered eight times through {page}, each option with a colour to tell it apart',
      'start/nested'              => 'A {page} inside a {page}, the shortest form of the chains beside it',
      'start/code/index'          => 'The eight {code} variants side by side',
      'start/page/index'          => 'The eight {page} chains side by side, which is what makes their differences readable',
      'start/combi/index'         => 'The three combi chains together',
      'start/combi1/index'        => 'A {page} chain mixing the options rather than using one throughout',
      'start/combi2/index'        => 'Another mixture of them',
      'start/combi3/index'        => 'A third mixture',
      'start/page/1/index'        => 'Nine nested {page} calls setting a variable - with no option it leaks outward',
      'start/page/2/index'        => 'The same chain with sandbox, so each pass has its own scope and the value unwinds',
      'start/page/3/index'        => 'The same chain with reset',
      'start/page/4/index'        => 'The same chain with clean',
      'start/page/5/index'        => 'The same chain incrementing rather than setting, with no option',
      'start/page/6/index'        => 'Incrementing with sandbox',
      'start/page/7/index'        => 'Incrementing with reset',
      'start/page/8/index'        => 'Incrementing with clean',
      'start/code/set/base'       => 'Nine nested {code} passes, each one setting the variable',
      'start/code/set/reset'      => 'Nine nested {code} passes, each one setting the variable, with reset',
      'start/code/set/clean'      => 'Nine nested {code} passes, each one setting the variable, with clean',
      'start/code/set/sandbox'    => 'Nine nested {code} passes, each one setting the variable, with sandbox',
      'start/code/increment/base' => 'Nine nested {code} passes, each one incrementting the variable',
      'start/code/increment/reset' => 'Nine nested {code} passes, each one incrementting the variable, with reset',
      'start/code/increment/clean' => 'Nine nested {code} passes, each one incrementting the variable, with clean',
      'start/code/increment/sandbox' => 'Nine nested {code} passes, each one incrementting the variable, with sandbox',
      'tableFun/fun_1_a'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_1_b'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_2_a'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_3_a'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_3_b'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_5_a'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_5_b'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_8'            => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_9'            => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_0'            => 'The {content} definition the other nine print their cells through - it renders nothing itself',

    ];

  }


  // Fetches every test and compares. The comparison is exact apart from the whitespace at the two
  // ends, which is what a template's own line breaks leave and says nothing about the page.
  //
  // Two expectations are not a body. A file holding nothing but HTTP <code> asserts the response
  // code and ignores what came with it, which is the only thing worth asserting about a page that
  // exists to fail: the error dump carries a request id and absolute paths, so it is different
  // every run and on every machine. A file with slashes at both ends is a regular expression, the
  // same convention the sandbox uses, for a page that draws a different answer each time.

  function getPagesRun () {

    $tests  = [];
    $total  = 0;
    $failed = 0;

    foreach ( getPagesList () as $name ) {

      set_time_limit ( 60 );

      $url  = getPagesUrl      ( $name );
      $want = getPagesWantFile ( $name );
      $curl = padCurl          ( $url );

      $got  = trim ( $curl ['data'] );
      $code = $curl ['result'];

      if ( ! file_exists ( $want ) ) {

        $status = 'new';
        $expect = '';

      } else {

        $expect = trim ( padFileGet ( $want ) );

        if ( str_starts_with ( $expect, 'HTTP ' ) ) {

          $ok  = ( trim ( substr ( $expect, 5 ) ) === (string) $code );
          $got = "HTTP $code";

        } elseif ( strlen ( $expect ) > 1 and str_starts_with ( $expect, '/' ) and str_ends_with ( $expect, '/' ) ) {

          $ok = (bool) preg_match ( $expect, $got );

          if ( $ok )
            $got = "matches $expect";

        } else

          $ok = ( $got === $expect and str_starts_with ( $code, '2' ) );

        $status = $ok ? 'ok' : 'FAILED';

      }

      $total++;

      if ( $status == 'FAILED' )
        $failed++;

      $tests [] = [
        'name'   => $name,
        'url'    => $url,
        'what'   => getPagesWhat ( $name ),
        'show'   => "?show&app=regression&item=pages/$name",
        'want'   => htmlspecialchars ( $expect ),
        'got'    => htmlspecialchars ( $got    ),
        'status' => $status,
        'failed' => ( $status == 'FAILED' ) ? 1 : 0
      ];

    }

    return [
      'tests'   => $tests,
      'summary' => "$total pages, $failed failed",
      'failed'  => $failed,
      'when'    => time ()
    ];

  }


  // Kept beside the sandbox runs, under the same name a group would have.

  function getPagesFile () {

    return DATA . 'suites/pages.json';

  }


  // Runs the pages suite, and like getRegressionSandbox() asks for it over HTTP from anywhere but
  // the regression application. Each test is a fetch either way, but getPagesWhat() and the
  // directory walk are APP-relative through getRegressionApp(), and a page under test may read
  // its own application's resources - so it is run where it lives.

  function getRegressionPages () {

    global $padHost;

    if ( APP != getRegressionApp () )
      return padCurl ( $padHost . 'regression/?pages/index&test' );

    set_time_limit ( 60 );

    $result = getPagesRun ();

    padFilePut ( getPagesFile (), json_encode ( $result ) );

    return $result;

  }


  // What a page load reads: the last run, without starting a new one. Fetching every test is a
  // request each, so this matters more here than it does for the sandbox - opening the overview
  // must not put the server through its own suite.

  function getPages () {

    $file = getPagesFile ();

    if ( file_exists ( $file ) ) {

      $result = json_decode ( padFileGet ( $file ), TRUE );

      if ( is_array ( $result ) and isset ( $result ['tests'] ) )
        return $result;

    }

    return getRegressionPages ();

  }


  // The runner behind every page under sandbox/: it renders each case in a group and
  // compares what the engine produced against what it is supposed to produce.
  //
  // The pages of the manual demonstrate the framework; they do not assert anything, so a
  // change that quietly alters a result still renders and still looks right. Every case here
  // states its expected output, which makes a difference a failure rather than a new baseline.
  //
  // A case is [ name, template, expected ], laid out one statement to a line. See
  // _cases/README.md for the format and for what this kind of suite cannot check.
  //
  // $seqFixture is the list the 'scope' cases in sequence/library.php iterate, and $objFixture
  // the variable the object: case in expressions/references.php reads. Both are declared here
  // rather than on one page because the overview runs every group as well, and padCode() renders
  // its pass over the globals - a variable local to getCases() or set on one page only would
  // leave the other page reporting a missing field.
  //
  // $objFixture cannot be handed over as a fourth-element setup the way other data is: object:
  // reads $GLOBALS, and a sandboxed pass has the application globals taken out of it, which is
  // what sandboxing means. So that case renders in this file's scope instead.

  $seqFixture = [ 1, 2, 3, 4, 5 ];
  $objFixture = [ 'a', 'b' ];

  function getCasesRun ( $group ) {

    $tests  = [];
    $total  = 0;
    $failed = 0;
    $dir    = getRegressionApp () . "sandbox/_cases/$group";

    if ( ! is_dir ( $dir ) )
      return [ 'tests' => [], 'summary' => "no cases for '$group'", 'failed' => 1, 'when' => 0 ];

    foreach ( padFiles ( $dir ) as $file ) {

      if ( ! str_ends_with ( $file, '.php' ) )
        continue;

      foreach ( include "$dir/$file" as $case ) {

        list ( $name, $code, $want ) = $case;

        // Each case is rendered in its own scope, so one cannot see or disturb the next and
        // the order they run in cannot change the outcome. A case that needs a variable or a
        // stored value therefore sets it up itself, in the same template.
        //
        // A fourth entry says how, when the template alone cannot:
        //
        //   'scope'   render with padCode() rather than padSandbox(), sharing the page's own
        //             variables. That is the only way to reach engine code no tag calls -
        //             pqTruncate() is reached through the trim option and never by a sequence
        //             tag - and the page sets up whatever those cases read.
        //
        //   an array  variables to put in place first, name => value. A nested pass binds the
        //             globals that exist when it opens, so these are visible to the template
        //             even sandboxed, and arrays of any depth come through - {$a.b.c} reads
        //             one. They are dropped again afterwards, so a case still cannot leave
        //             anything behind for the next.
        //
        // The array form is what lets a page whose data came from a paired .php file be a case
        // at all: the data is stated in the case instead, where it can be read beside what it
        // is supposed to produce.

        $setup = $case [3] ?? '';

        if ( is_array ( $setup ) )
          foreach ( $setup as $setupName => $setupValue )
            $GLOBALS [$setupName] = $setupValue;

        if ( $setup === 'scope' )
          $got = padCode    ( $code );
        else
          $got = padSandbox ( $code );

        if ( is_array ( $setup ) )
          foreach ( array_keys ( $setup ) as $setupName )
            unset ( $GLOBALS [$setupName] );

        // A tag that produces PAD's own syntax characters - {ignore}, the open/close/tag
        // functions, a tag left standing because nothing claimed it - hands them back escaped
        // as &open; and &close;, and exits/exits.php turns those into braces once the whole
        // request is written. That has not happened yet here, so it is done to the case's
        // output first: a case then states what the page shows rather than an intermediate
        // spelling of it.

        $got = padUnescape ( $got );

        // A case is written one statement to a line, so the engine emits the line break and
        // the indentation that follow each of them - once per term for anything inside a
        // loop. That whitespace is the shape of the case, not an answer the framework gave,
        // so it comes out before the comparison: a line break and the indentation after it
        // go, and spacing written within a line stays, which is the only spacing a case can
        // be asserting.

        $got  = preg_replace ( '/\n\s*/', '', $got );
        $show = $got;

        // A regular expression is recognised by slashes at both ends, not just at the front:
        // '/test' is a value afterLast produces, and testing only the first character turned
        // that expectation into an unterminated pattern.

        if ( strlen ( $want ) > 1 and str_starts_with ( $want, '/' ) and str_ends_with ( $want, '/' ) ) {

          $ok = (bool) preg_match ( $want, $got );

          // A case pinned by shape answers differently every run, so a passing one reports
          // the shape it matched rather than the values it drew. Without that this page would
          // never render the same twice, and the regression run that compares it against its
          // previous output would report it changed on every pass.

          if ( $ok )
            $show = "matches $want";

        }

        else
          $ok = ( $got === $want );

        $total++;

        if ( ! $ok )
          $failed++;

        $tests [] = [
          'group'  => str_replace ( '.php', '', $file ),
          'name'   => $name,
          'code'   => getCasesShow ( $code ),
          'want'   => htmlspecialchars ( $want ),
          'got'    => htmlspecialchars ( $show ),
          'status' => $ok ? 'ok' : 'FAILED',
          'failed' => $ok ? 0 : 1
        ];

      }

    }

    // The count is kept apart from the per-row 'failed' field, and the pages take it as
    // $failedCount: a page variable sharing a name with a field of the rows it iterates is the
    // collision CLAUDE.md warns about, and this template reads both.

    return [
      'tests'   => $tests,
      'summary' => "$total tests, $failed failed",
      'failed'  => $failed,
      'when'    => time ()
    ];

  }


  // Where a run is kept. DATA is the writable tree and is not in git, which is what a result
  // that is regenerated on demand should be; the crawler's own baselines live beside it under
  // regression/, so the suites get a directory of their own rather than sharing that one.

  function getCasesFile ( $group ) {

    return DATA . "suites/$group.json";

  }


  // Runs a group and keeps the result. This is what the Test link asks for, and the only thing
  // that runs a case at all.

  function getCasesTest ( $group ) {

    $result = getCasesRun ( $group );

    padFilePut ( getCasesFile ( $group ), json_encode ( $result ) );

    return $result;

  }


  // What a page load reads: the last run, without starting a new one. A group that has never
  // been run has nothing to show, so that one is run and kept - after which a page load is a
  // page load again.

  function getCases ( $group ) {

    $file = getCasesFile ( $group );

    if ( file_exists ( $file ) ) {

      $result = json_decode ( padFileGet ( $file ), TRUE );

      if ( is_array ( $result ) and isset ( $result ['summary'] ) )
        return $result;

    }

    return getCasesTest ( $group );

  }


  // The template is shown coloured, through the padColorsString() the {demo} tag uses, so a
  // case reads the same way as the examples in the manual. The colouring hands back a <pre>,
  // which never wraps; a case can run past a hundred characters, and with its result beside it
  // the table would leave the window, so that block is allowed to wrap.

  function getCasesShow ( $code ) {

    if ( ! function_exists ( 'padColorsString' ) )
      return '<pre style="margin:0">' . htmlspecialchars ( $code ) . '</pre>';

    return str_replace ( '<pre>', '<pre style="white-space:pre-wrap; margin:0">',
                         padColorsString ( $code ) );

  }


  // Runs the sandbox suite. From anywhere but the regression application itself, it is asked for
  // over HTTP rather than run here.
  //
  // A case is rendered by padSandbox() inside the request that is running, so it reaches the
  // _tags, _callbacks, _functions and _data of whatever application that is. Driven from
  // develop's build page the cases were found - their directory is named outright now - but
  // their fixtures were not: {n callback='before.php'} looked for a _callbacks/ develop does not
  // have, the callback never ran, and the case died on the field it should have set. Asking for
  // it over HTTP runs it in the application it belongs to, which is the only place it can work.

  function getRegressionSandbox () {

    global $padHost;

    if ( APP != getRegressionApp () )
      return padCurl ( $padHost . 'regression/?sandbox/index&test' );

    set_time_limit ( 60 );

    foreach ( array_keys ( getCasesGroups () ) as $groupName )
      getCasesTest ( $groupName );

  }


  // The list every page under sandbox/ is built from, and what the overview walks.

  function getCasesGroups () {

    return [
      'tags'        => 'Control flow, output and the tags that carry them',
      'functions'   => 'The pipe functions, over the values they document',
      'options'     => 'Tag options, each against the same tag without it',
      'properties'  => 'The property@tag values an iteration publishes',
      'expressions' => 'Comparison, arithmetic, ranges and the @ placeholder',
      'variables'   => 'Assignment, scope, arrays and the two variable kinds',
      'data'        => 'The data tag over JSON, XML, CSV and a plain list',
      'prefixes'    => 'The type prefixes that say what a name means',
      'escaping'    => 'What stops PAD reading braces as tags, in each of its spellings',
      'custom'      => 'What an application supplies: _tags, _functions, _include, _data',
      'check'       => 'Pages carried over from the check application, which is gone',
      'sequence'    => 'The sequence subsystem - types, actions, plays, stores and options',
    ];

  }

?>