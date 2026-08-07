<?php

  // The runner behind pages/, the second kind of regression test in this application.
  //
  // A sandbox case is a template string rendered inside the running request. That is quick and
  // self-contained, but it is not a request: a nested pass has its own variable scope, so a page
  // whose .php file leaves something behind for its .pad file cannot be written as one, and
  // neither can anything that reads $GLOBALS or depends on how a request is built.
  //
  // A pages test is an ordinary page of this application - name.php with an optional name.pad -
  // fetched over HTTP exactly as a browser would, with &padInclude so it renders bare, without
  // the menu and title the wrapper puts round it. What comes back is compared with name.txt,
  // written beside it.
  //
  // name.txt is written by hand and nothing here ever rewrites it. That is the whole point: an
  // expectation that the harness records for itself is not a prediction, it is a copy of
  // whatever the code did, and a change to it would be recorded rather than reported. A test
  // with no name.txt yet comes up 'new' and the overview shows exactly what came back, which is
  // what goes in the file.

  function getPagesDir () {

    return APP . 'pages/';

  }


  // Every page under pages/ except the overview itself and anything an underscore hides. A test
  // is named by its .php, which is the one file it must have; the .pad half is optional, so the
  // listing cannot be taken from the templates.

  function getPagesList () {

    $dir  = getPagesDir ();
    $list = [];

    if ( ! is_dir ( $dir ) )
      return $list;

    foreach ( padFiles ( $dir ) as $file ) {

      if ( ! str_ends_with ( $file, '.php' ) ) continue;
      if ( str_starts_with ( $file, '_'    ) ) continue;

      $name = substr ( $file, 0, -4 );

      if ( $name == 'index' ) continue;

      $list [] = $name;

    }

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


  // Fetches every test and compares. The comparison is exact apart from the whitespace at the
  // two ends, which is what a template's own line breaks leave and says nothing about the page.

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

      if ( ! str_starts_with ( $code, '2' ) ) {

        $status = 'FAILED';
        $expect = "http $code";

      } elseif ( ! file_exists ( $want ) ) {

        $status = 'new';
        $expect = '';

      } else {

        $expect = trim ( padFileGet ( $want ) );
        $status = ( $got === $expect ) ? 'ok' : 'FAILED';

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
