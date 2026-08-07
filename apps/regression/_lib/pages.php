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
        'pad'    => file_exists ( getPagesDir () . "$name.pad" ) ? 'php + pad' : 'php only',
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