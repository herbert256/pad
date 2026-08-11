<?php

  // The harness cases test the regression application's own runner, so its library is
  // included here - it lives in the regression application because that is whose
  // behaviour the harness group asserts.

  include_once APPS . '_common/_lib/apps.php';
  include_once APPS . 'regression/main/_lib/regression.php';
  include_once APPS . 'regression/main/_lib/what.php';
  include_once APPS . 'regression/main/_lib/returns.php';

  // The walkers live with _common, which this application switches off, so the include is
  // spelled out - the parity and discovery cases walk like the runner walks.

  // Fault-injection helpers: each hands the runner a synthetic input and returns what it
  // decided, printable on one line. What they prove is the verdict machinery itself -
  // the comparison modes, the state words, a fetch that never answered - not any page.

  $GLOBALS ['harnessHttpPattern'] = "HTTP 500\n/boom/";

  function harnessVerdictOfLast ( ) {

    $last = getSuiteLast ( 'no_such_suite' );

    return $last ['summary'] . ' / ' . getSuiteVerdict ( $last );

  }

  function harnessCompare ( $expect, $got, $code, $body ) {

    list ( $ok, $out ) = getSuiteCompare ( $expect, $got, $code, $body );

    return ( $ok ? 'ok' : 'no' ) . ':' . $out;

  }

  function harnessResult ( ) {

    $rows = [
      [ 'status' => 'ok',     'failed' => 0, 'count' => 1 ],
      [ 'status' => 'FAILED', 'failed' => 1, 'count' => 1 ],
      [ 'status' => 'new',    'failed' => 0, 'count' => 1 ],
    ];

    return getSuiteResult ( $rows ) ['summary'];

  }

  function harnessVerdict ( $when, $failed, $new ) {

    return getSuiteVerdict ( [ 'when' => $when, 'failed' => $failed, 'new' => $new ] );

  }

  function harnessDropped ( ) {

    $want = APPS . 'regression/framework/harness/a_test_url_carries_padInclude.txt';

    $test = getSuiteOne ( 'regression/framework', 'harness/x', 'unfetched', $want,
                          [ 'data' => '', 'result' => '999' ] );

    return $test ['status'] . ' HTTP ' . 999;

  }

  function harnessBoundary ( $path ) {

    list ( $app, $item ) = padAppBoundary ( $path );

    return "$app : $item";

  }

  function harnessDiscovery ( $page ) {

    return isset ( padAppsList () [$page] ) ? 'listed' : 'absent';

  }

  // The fixtures several case groups read. $seqFixture is the list sequence/library.php
  // iterates to reach pqTruncate(), which no sequence tag goes near, and $objFixture is
  // what the object: case in the expressions group looks up. $savedFixture is what the
  // @saved case shadows. The harness counting cases read a catalogue-shaped answer (two
  // labelled lines, one continuation) and a rendering-shaped one - multiline values that
  // cannot be written inside a case page, so they live here.
  //
  // Written into $GLOBALS rather than assigned, because where a _lib file's top level
  // ends up is not fixed.

  $GLOBALS ['seqFixture']       = [ 1, 2, 3, 4, 5 ];
  $GLOBALS ['objFixture']       = [ 'a', 'b' ];
  $GLOBALS ['savedFixture']     = 'outer';

  $GLOBALS ['harnessLabelled']  = "one: a\ntwo: b\ncontinuation line";
  $GLOBALS ['harnessRendering'] = "<p>\n  a page\n</p>";

?>