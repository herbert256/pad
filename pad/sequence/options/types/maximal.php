<?php

  // Marker file, never included: its existence is what makes 'maximal' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  // inits/parms.php reads $pqParms['maximal'] into $pqMax, the build's upper bound; without
  // this file a tag that gave maximal= and no sequence type, {sequence maximal=5, rows=3},
  // was told 'maximal' is not a sequence type. Same for minimal.php.

  return TRUE;

?>
