<?php

  // Where the two suites live. They belong to the regression application wherever they are driven
  // from - develop's build page calls getRegression() as well - so the directories are named
  // absolutely rather than against APP.
  //
  // Against APP they resolved to whatever application was running. A build run from develop
  // looked for apps/develop/sandbox/_cases/ and apps/develop/pages/, found neither, and wrote
  // "no cases for 'tags'" over every stored result; the crawl then stored the suite pages saying
  // that, and getRegressionWarning() accepted it as the baseline. getPagesUrl() has always
  // named the application outright, so this only makes the rest of it agree.

  function getRegressionApp () {

    return APPS . 'regression/main/';

  }


  function getRegression ( $extra='' ) {

    getRegressionPages     ();
    getRegressionFramework ();
    getRegressionSuite     ();
    getRegressionAppSuites ();
    getRegressionOther     ();

    if ( $extra )
      getRegressionAll ( $extra );

  }


  // Runs the Regression suite, asked for over HTTP from anywhere but this application -
  // the same indirection getRegressionPages() uses, and for the same reason: the walk and
  // the predictions are APP-relative, so the suite runs where it lives.

  function getRegressionSuite () {

    global $padHost;

    if ( APP != getRegressionApp () )
      padCurl ( $padHost . "regression/main/?regression/index&test" );
    else
      getPagesTest ( 'regression' );

  }


  // The Other suite: every application without a suite of its own - whatever
  // padAppsList() names outside the regression family and the application suites. A new
  // application lands here as 'new' until its predictions are written under
  // apps/regression/other/<app>/.

  function getOtherSuiteRun () {

    $tests = [];

    foreach ( padAppsList () as $one ) {

      if ( str_starts_with ( $one ['app'], 'regression/' ) or in_array ( $one ['app'], getAppSuites () ) )
        continue;

      set_time_limit ( 60 );

      $want = APPS . 'regression/other/' . $one ['app'] . '/' . $one ['item'] . '.txt';

      $test = getPagesOne ( $one ['app'], $one ['item'],
                            getPagesUrl ( $one ['app'], $one ['item'] ), $want );

      $test ['name'] = $one ['app'] . '/' . $one ['item'];
      $test ['what'] = '';

      $tests [] = $test;

    }

    return getPagesResult ( $tests );

  }


  function getRegressionOther () {

    global $padHost;

    if ( APP != getRegressionApp () )
      padCurl ( $padHost . "regression/main/?other/index&test" );
    else
      getPagesTest ( 'other' );

  }


  function getRegressionAppSuites () {

    global $padHost;

    foreach ( getAppSuites () as $suite )

      if ( APP != getRegressionApp () )
        padCurl ( $padHost . "regression/main/?$suite/index&test" );
      else
        getPagesTest ( $suite );

  }


  // The harvest walk: one page at a time over every application padAppsList() names -
  // the reference and the examples are gathered from the suite applications' pages too.
  // This is the only crawl left; comparing pages is the suites' business. It stays one
  // page at a time: with &padReference every page appends what it used to the shared
  // reference files as it renders, behind a read-check-append that is not safe against
  // itself running twelve-wide - and the harvest is one run per build.

  function getRegressionAll ( $extra ) {

    foreach ( padAppsList () as $one ) {

      set_time_limit ( 60 );

      getRegressionGo ( $one ['app'], $one ['item'], $extra );

    }

  }


  // Whether a page that answered with an error code was supposed to. The pages suites already
  // declare it - an expectation file opening with HTTP <code> sits beside the page - so the
  // crawl reads that declaration rather than asking the page for a marker of its own.

  function getRegressionExpects ( $app, $item, $code ) {

    $want = trim ( padFileGet ( APPS . "$app/$item.txt" ) );

    return str_starts_with ( $want, "HTTP $code" );

  }


  function getRegressionUrl ( $app, $item, $extra='' ) {

    global $padHost;

    $include = ( $item != 'index' ) ? '&padInclude' : '';

    return "$padHost$app/?$item$include$extra";

  }


  // The harvester behind getRegressionAll(): fetch the page with the harvest extras and
  // store what the examples application shows - the sources beside the tidied render. The
  // reference harvest rides the same fetch: &padReference makes the page itself record
  // what it used as it renders. The source is read for the markers that say a page is no
  // example - a body that is mostly another page's, a demo, an {ajax} shell - and a page
  // with no template keeps its markers in its .php, so all three halves are read.

  function getRegressionGo ( $app, $item, $extra='', $fetched=NULL ) {

    $curl = $fetched ?? padCurl ( getRegressionUrl ( $app, $item, $extra ) );

    if ( ! str_contains ( $extra, '&padExamples' ) or ! str_starts_with ( $curl ['result'], '2' ) )
      return;

    $source = padFileGet ( APPS . "$app/$item.pad"  )
            . padFileGet ( APPS . "$app/$item.html" )
            . padFileGet ( APPS . "$app/$item.php"  );

    if ( str_contains ( $source, '{page'    ) ) return;
    if ( str_contains ( $source, '{example' ) ) return;
    if ( str_contains ( $source, '{ajax'    ) ) return;
    if ( str_contains ( $source, '{table'   ) ) return;
    if ( str_contains ( $source, '{demo'    ) ) return;

    if ( file_exists ( APPS . "$app/$item.php" ) )
      padFilePut ( "examples/$app/$item.php",  padFileGet ( APPS . "$app/$item.php" ) );

    if ( file_exists ( APPS . "$app/$item.pad" ) )
      padFilePut ( "examples/$app/$item.pad",  padFileGet ( APPS . "$app/$item.pad" ) );
    elseif ( file_exists ( APPS . "$app/$item.html" ) )
      padFilePut ( "examples/$app/$item.pad",  padFileGet ( APPS . "$app/$item.html" ) );

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

      if ( $ext != 'pad' and $ext != 'php' and $ext != 'html' )
        continue;

      // A page with no template is still a page - what its .php echoes is its output - and the
      // crawl had never seen one. Nineteen of them across the tree, the five app-level error
      // raisers among them, and none had a stored copy or an entry on the scan. A pair keys by the
      // same name either way, so it still counts once.
      //
      // Except where the .php is not a page but an action. ?ok accepts a baseline, todoPost
      // writes one, structure/index sends you elsewhere - fetching those changes something, and
      // a crawl has to be able to run without changing anything. So a page with no template that
      // redirects, restarts or writes is left out.

      if ( $ext == 'php' and ! file_exists ( substr ( $path, 0, -4 ) . '.pad' )
                         and ! file_exists ( substr ( $path, 0, -4 ) . '.html' ) ) {

        $source = padFileGet ( $path );

        if ( str_contains ( $source, 'padRedirect'      )
          or str_contains ( $source, 'padRestart'       )
          or str_contains ( $source, 'padFilePut'       )
          or str_contains ( $source, 'padDeleteDataDir' ) )
          continue;

      }

      $file  = str_replace ( APPS, '', $path );

      // The application is the shortest leading run of directories with an index page of
      // its own. A directory holding only applications, like regression/, is a namespace,
      // and an app below it keeps the namespace in its name: 'regression/main'.

      $parts = explode ( '/', dirname ( $file ) );
      $app   = array_shift ( $parts );

      while ( $parts and ! padAppsListRoot ( $app ) )
        $app .= '/' . array_shift ( $parts );

      $item  = substr ( $file, strlen ( $app ) + 1 );
      $item  = substr ( $item, 0, strrpos ( $item, '.' ) );

      $files ["$app/$item"] ['path'] = $path;
      $files ["$app/$item"] ['app']  = $app;
      $files ["$app/$item"] ['item'] = $item;

    }

    ksort ($files);

    return $files;

  }


  // An application is a name with an entry point: www/<app>/index.php is what makes a name
  // fetchable, so it is also what draws the app boundary inside a nested name - the
  // directory above regression/main has no entry of its own, which is what makes it a
  // namespace rather than an app.

  function padAppsListRoot ( $app ) {

    static $roots = [];

    return $roots [$app] ??= file_exists ( dirname ( APPS ) . "/www/$app/index.php" );

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
  // A test can also be a whole directory - see getPagesList() for when an index stands for the
  // files beside it and when it does not.
  //
  // name.txt is written by hand and nothing here ever rewrites it. That is the whole point: an
  // expectation that the harness records for itself is not a prediction, it is a copy of
  // whatever the code did, and a change to it would be recorded rather than reported. A test
  // with no name.txt yet comes up 'new' and the overview shows exactly what came back, which is
  // what goes in the file.

  function getPagesSuites () {

    return [ 'pages' => 'regression/pages', 'common' => 'regression/common' ];

  }


  function getPagesDir ( $app ) {

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
  // without &padInclude - see getPagesUrl() - so what it asserts is the frame itself: the
  // _common wrapper, the title its _inits.php derives, the tidied whole. Neither application
  // is obliged to have one; regression/pages does not, since its frame is no frame at all.

  function getPagesList ( $app ) {

    return getPagesWalk ( getPagesDir ( $app ), '' );

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

      elseif ( str_ends_with ( $file, '.html' ) )
        $names [ substr ( $file, 0, -5 ) ] = TRUE;

    }

    if ( $prefix and isset ( $names ['index'] ) and getPagesRenders ( $dir ) )
      $names = [ 'index' => TRUE ];

    foreach ( array_keys ( $names ) as $name )
      $list [] = $prefix . $name;

    foreach ( $dirs as $one )
      $list = array_merge ( $list, getPagesWalk ( "$dir$one/", "$prefix$one/" ) );

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

  function getPagesUrl ( $app, $name ) {

    global $padHost;

    $include = ( $name != 'index' ) ? '&padInclude' : '';

    return $padHost . "$app/?$name$include";

  }


  function getPagesWantFile ( $app, $name ) {

    return getPagesDir ( $app ) . "$name.txt";

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

  function getPagesWhat ( $app, $name ) {

    $list = getPagesWhatList ();

    return $list ["$app/$name"] ?? $list [$name] ?? '';

  }


  function getPagesWhatList () {

    return [

      'index'                     => 'The one test fetched without padInclude: the _common wrapper, padOpen and padClose with the menu, and the title shown through showTitle',
      'menu'                      => 'The {menu} include - lines.pad around it, menu.json behind it, a link per application',
      'reference'                 => 'The _common _lib helpers a page calls directly - getReference() over the type handlers, and the Xref link builders',
      'misc/db'                   => 'The demo database through the credentials _common supplies - the one test that queries it',
      'regression/pages/error/index'   => 'The {error} tag with _common switched off - ending a request needs nothing shared',
      'error/throw'               => 'The {exception} tag, which throws a real PHP exception from the template',
      'error/exit'                => 'The {exit} tag, which ends the request where it stands - and ships nothing at all, which the pattern pins',
      'error/dump'                => 'The {dump} tag, which stops the request on the engine state dump',
      'misc/trace'                => 'The {trace} tag wrapping a scope in the execution trace',
      'misc/react'                => 'The {reactData} tag over a static provider, and the @providers reference reading the parked result back',
      'select/support/prefix'     => 'The select: prefix spelling of a declared table, which also says the word the reference matcher looks for',
      'select/demo/options'       => 'Every option a declared table takes on the tag - where, order, fields, group, having, rollup, distinctrow, db and union - one labelled line each over the staff table',
      'catalog/tags'              => 'One line per built-in tag no other page reaches - the catalogue half of the reference coverage',
      'catalog/prefixes'          => 'One line per type prefix, each in its literal spelling',
      'catalog/properties'        => 'Every property and @ reference form on one page',
      'catalog/options'           => 'One line per tag option, including the ones that assert being inert',
      'catalog/functions'         => 'One line per pipe function over a stated value',
      'catalog/handling'          => 'The data handling options, each over a list whose answer is obvious',
      'catalog/volatile'          => 'The two draws - {ajax} ids and now - pinned by shape in one regex',
      'catalog/common'            => 'The common: prefix reaching the shared application by name - the one catalogue line that needs _common',
      'callback'                  => 'The row phase of a streaming callback, which reads the occurrence fields as plain variables',
      'globals'                   => 'object: over a page variable, which in a request is a global like any other',
      'pairing'                   => 'A page pair: the .php half fills in what the .pad half prints',
      'phponly'                   => 'A page with no template at all - what its .php echoes is the page',
      'htmlpage'                  => 'An .html file as the template half of a pair - variables, pipes, a loop and a property all resolve in it',
      'htmlboth'                  => 'A page with both a .pad and an .html - the .pad wins',
      'htmlonly'                  => 'An .html page with no .php half, carrying a tag of its own',
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
      'error/error_1'             => 'An engine-raised PHP warning from a tag - an undefined variable - which $padErrorLevel promotes to an ended request',
      'error/error_2'             => 'The same engine-raised warning from the page rather than from a tag',
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
      'misc/datatypes'            => 'The html and range data types: a tidied document walked to its values, and both range spellings',
      'manual/3_ways_to_make_a_table' => 'The start, end and else constructs over one table',
      'manual/doc3'               => 'Tag properties read through a content store',
      'manual/name'               => 'How PAD names a tag, and the name option that overrides it',
      'manual/table_fun'          => 'Ten ways of showing one table, each embedding one of the tableFun pages',
      'manual/variable_kinds'     => 'The variable prefixes, as far as the manual page got',
      'manual/z99'                => 'A page whose .php returns a string, which the build prepends to the page',
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

  function getPagesCount ( $name, $expect ) {

    if ( ! str_starts_with ( $name, 'catalog/' ) )
      return 1;

    return max ( 1, preg_match_all ( '/^\S+: /m', $expect ) );

  }


  function getPagesRun ( $app ) {

    $tests = [];

    foreach ( getPagesList ( $app ) as $name ) {

      set_time_limit ( 60 );

      $tests [] = getPagesOne ( $app, $name, getPagesUrl ( $app, $name ), getPagesWantFile ( $app, $name ) );

    }

    return getPagesResult ( $tests );

  }


  // One test: fetch the url, compare with the answer file, return the row the overviews
  // render. The caller says where both live, which is what lets the Regression suite hold
  // its predictions apart from its pages.

  function getPagesOne ( $app, $name, $url, $want ) {

    $curl = padCurl ( $url );

    $got  = trim ( $curl ['data'] );
    $code = $curl ['result'];

    if ( ! file_exists ( $want ) ) {

      $status = 'new';
      $expect = '';

    } else {

      $expect = trim ( padFileGet ( $want ) );

      if ( str_starts_with ( $expect, 'HTTP ' ) ) {

        $expectLines   = padExplode ( $expect, "\n" );
        $expectCode    = trim ( substr ( $expectLines [0], 5 ) );
        $expectPattern = $expectLines [1] ?? '';

        $ok  = ( $expectCode === (string) $code );
        $got = "HTTP $code";

        if ( $ok and $expectPattern ) {

          $ok = (bool) preg_match ( $expectPattern, trim ( $curl ['data'] ) );

          $got = ( $ok ) ? "HTTP $code, matches $expectPattern"
                         : "HTTP $code\n" . trim ( $curl ['data'] );

        }

      } elseif ( strlen ( $expect ) > 1 and str_starts_with ( $expect, '/' ) and str_ends_with ( $expect, '/' ) ) {

        // A pattern answer still needs a healthy response: without the status check a
        // fragment surviving inside an error dump could pass a test that meant to assert
        // a working page. A failing status is asserted with the HTTP form instead.

        $ok = (bool) preg_match ( $expect, $got ) and str_starts_with ( (string) $code, '2' );

        if ( $ok )
          $got = "matches $expect";

      } else

        $ok = ( $got === $expect and str_starts_with ( $code, '2' ) );

      $status = $ok ? 'ok' : 'FAILED';

    }

    return [
      'name'   => $name,
      'url'    => $url,
      'what'   => getPagesWhat ( $app, $name ),
      'want'   => htmlspecialchars ( $expect ),
      'got'    => htmlspecialchars ( $got    ),
      'status' => $status,
      'failed' => ( $status == 'FAILED' ) ? 1 : 0,
      'count'  => getPagesCount ( $name, $expect )
    ];

  }


  // A test with no recorded answer is not passing - it is waiting for one. It has its own
  // count rather than a place in 'failed', so the overview can say which it is, and ci.sh
  // gates on both.

  function getPagesResult ( $tests ) {

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

  function getPagesFile ( $suite ) {

    return DATA . "suites/$suite.json";

  }


  // Runs one suite and keeps the result. This is what the Test link on a suite page asks for,
  // and the only thing that runs a suite at all.

  function getPagesTest ( $suite ) {

    set_time_limit ( 60 );

    $result = match ( TRUE ) {
      $suite == 'regression'                   => getRegressionSuiteRun (),
      $suite == 'other'                        => getOtherSuiteRun (),
      in_array ( $suite, getAppSuites () )     => getAppSuiteRun ( $suite ),
      default                                  => getPagesRun ( getPagesSuites () [$suite] ),
    };

    padFilePut ( getPagesFile ( $suite ), json_encode ( $result ) );

    return $result;

  }


  // The Regression suite: every page of the self-testing applications - the cache, error,
  // output, info, config and try families under regression/ - fetched exactly as the crawl
  // fetches a page, and compared against the prediction of the same name in
  // apps/regression/regression/: the page regression/cache_apcu/probe answers what
  // cache_apcu/probe.txt predicts. The pages stay in their own applications; the store
  // holds nothing but the predictions, handwritten like every suite answer.
  //
  // The suite runs its pages one request at a time, which is the isolation their solo
  // crawl used to provide: these pages prove their subsystem by fetching their own probe
  // from inside the request, and a concurrent fetch could land between an index's two
  // probes and turn a working backend into a NO.

  function getRegressionSuiteApps () {

    $apps = [];

    foreach ( padFiles ( APPS . 'regression/' ) as $one )
      if ( ! str_starts_with ( $one, '_' )
           and is_dir ( APPS . "regression/$one" )
           and ! in_array ( $one, [ 'pages', 'common', 'framework', 'regression' ] ) )
        $apps [] = "regression/$one";

    return $apps;

  }


  function getRegressionSuiteRun () {

    $covered = getRegressionSuiteApps ();
    $tests   = [];

    foreach ( padAppsList () as $one ) {

      if ( ! in_array ( $one ['app'], $covered ) )
        continue;

      set_time_limit ( 60 );

      $short = substr ( $one ['app'], strlen ( 'regression/' ) );
      $want  = APPS . 'regression/regression/' . $short . '/' . $one ['item'] . '.txt';

      $test = getPagesOne ( $one ['app'], $one ['item'],
                            getPagesUrl ( $one ['app'], $one ['item'] ), $want );

      $test ['name'] = $short . '/' . $one ['item'];
      $test ['what'] = getPagesWhatList () [ $one ['app'] . '/' . $one ['item'] ] ?? '';

      $tests [] = $test;

    }

    return getPagesResult ( $tests );

  }


  // The application suites: the Regression-suite model over one application outside the
  // family, a suite each - sequence and manual so far. Every page of the application is
  // fetched as the crawl used to fetch it and compared against the prediction of the same
  // name in apps/regression/<app>/. A page that draws answers a /pattern/ pinning its
  // skeleton; everything else an exact body. The suite key, the covered application and
  // the store directory all share the name.

  function getAppSuites () {

    return [ 'sequence', 'manual' ];

  }


  function getAppSuiteRun ( $app ) {

    $tests = [];

    foreach ( padAppsList () as $one ) {

      if ( $one ['app'] != $app )
        continue;

      set_time_limit ( 60 );

      $want = APPS . "regression/$app/" . $one ['item'] . '.txt';

      $test = getPagesOne ( $one ['app'], $one ['item'],
                            getPagesUrl ( $one ['app'], $one ['item'] ), $want );

      $test ['what'] = '';

      $tests [] = $test;

    }

    return getPagesResult ( $tests );

  }


  // Runs both suites, asked for over HTTP from anywhere but the regression application.
  // Each test is a fetch either way, but getPagesWhat() and the
  // directory walk are APP-relative through getRegressionApp(), and a page under test may read
  // its own application's resources - so they are run where they live.

  function getRegressionPages () {

    global $padHost;

    foreach ( array_keys ( getPagesSuites () ) as $suite )

      if ( APP != getRegressionApp () )
        padCurl ( $padHost . "regression/main/?$suite/index&test" );
      else
        getPagesTest ( $suite );

  }


  // What a page load reads: the last run, without starting a new one - a missing or
  // unreadable result answers 'never run', and Test is the only thing that runs. Fetching
  // every test is a request each, so opening a report must never cost a suite.

  function getPages ( $suite ) {

    $file = getPagesFile ( $suite );

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

          if ( strlen ( $expect ) > 1 and str_starts_with ( $expect, '/' ) and str_ends_with ( $expect, '/' ) ) {

            $ok = (bool) preg_match ( $expect, $got ) and str_starts_with ( (string) $code, '2' );

            if ( $ok )
              $got = "matches $expect";

          } else

            $ok = ( $got === $expect and str_starts_with ( (string) $code, '2' ) );

          $status = $ok ? 'ok' : 'FAILED';

        }

        $total++;
        $groups [ substr ( $name, 0, strpos ( $name, '/' ) ) ] = TRUE;

        if ( $status == 'FAILED' ) $failed++;
        if ( $status == 'new'    ) $new++;

        $tests [] = [
          'name'   => $name,
          'url'    => $url,
          'want'   => htmlspecialchars ( $expect ),
          'got'    => htmlspecialchars ( $got    ),
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


  function getFrameworkTest () {

    set_time_limit ( 60 );

    $result = getFrameworkRun ();

    padFilePut ( getPagesFile ( 'framework' ), json_encode ( $result ) );

    return $result;

  }


  function getRegressionFramework () {

    global $padHost;

    if ( APP != getRegressionApp () )
      padCurl ( $padHost . "regression/main/?framework/index&test" );
    else
      getFrameworkTest ();

  }


  // The stored status of every page the scan keeps, counted by status. The index page reports
  // these totals; the scan page builds its fuller list itself.

  function getScanCounts () {

    $counts = [];

    $dir = DATA . 'regression/';

    if ( ! is_dir ( $dir ) )
      return $counts;

    $directory = new RecursiveDirectoryIterator ( $dir );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    foreach ( $iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname () );

      if ( ! str_ends_with ( $path, '.txt' ) )
        continue;

      $status = trim ( padFileGet ( $path ) );

      $counts [$status] = ( $counts [$status] ?? 0 ) + 1;

    }

    ksort ( $counts );

    return $counts;

  }

?>