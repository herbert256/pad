<?php

  // The harness cases test the regression application's own runner, so its library is
  // included here - it lives in the regression application because that is whose
  // behaviour the harness group asserts.

  include_once APPS . 'regression/main/_lib/regression.php';
  include_once APPS . 'regression/main/_lib/returns.php';

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