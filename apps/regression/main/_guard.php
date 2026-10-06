<?php

  // The runner acts on a plain GET: Test runs suites - thousands of fetches - and wipes
  // DATA/dumps and DATA/temp, Build wipes the suite results first, and record writes an
  // answer into a store under apps/. Those answer this machine's own requests only - the
  // command line, or loopback without a forwarding header, as padSelfSwitch has it - where
  // any visitor could start them, and any page a developer opened could with an <img src>.
  // ci.sh, develop's build and the Test links of a local browser are this machine; reading
  // the overviews stays open to everyone, the static copy pages.sh makes among them.

  if ( isset ( $test ) or isset ( $go ) or $padPage == 'record' )
    return PHP_SAPI === 'cli' or padLoopback ();

?>
