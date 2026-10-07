<?php

  // The runner acts on a plain GET: Test runs suites - thousands of fetches - and wipes
  // DATA/dumps and DATA/temp, Build wipes the suite results first, and record writes an
  // answer into a store under apps/. Those answer this machine's own requests only -
  // getSuiteLocal, develop's rule: the command line, or loopback with nothing forwarded,
  // this machine's name in Host and no other site behind it - where any visitor could start
  // them, and any page a developer opened could with an <img src>. ci.sh, develop's build
  // and the Test links of a local browser are this machine; reading the overviews stays
  // open to everyone, the static copy pages.sh makes among them.

  if ( getSuiteLocal () )
    return TRUE;

  // Everyone else reads the overviews - the index, the build page, a suite's index - and
  // nothing more: no Test, no go, no record. A list of what may be read, where the guard
  // named what may not: on a filesystem that ignores case ?Record reached record.php as
  // 'Record', and that name was not 'record'.

  if ( isset ( $test ) or isset ( $go ) )
    return FALSE;

  return $padPage == 'index' or $padPage == 'build'
      or ( str_ends_with ( $padPage, '/index' ) and isset ( getSuites () [ substr ( $padPage, 0, -6 ) ] ) );

?>
