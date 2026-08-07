<?php

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

    return APP . 'pages/';

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


  function getPagesTest () {

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

    return getPagesTest ();

  }

?>
