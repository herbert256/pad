<?php

  // Marker file, never included: its existence is what makes 'minimal' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  // inits/parms.php reads $pqParms['minimal'] into $pqMin, the build's lower bound; without
  // this file a tag that gave minimal= and no sequence type, {sequence minimal=5, rows=3},
  // was told 'minimal' is not a sequence type. Same for maximal.php.

  return TRUE;

?>