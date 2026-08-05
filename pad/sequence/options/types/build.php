<?php

  // Marker file, never included: its existence is what makes 'build' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // build='strategy' overrides the strategy pqBuild() infers from the type's directory
  // (loop, make, function, bool, order, build, fixed). inits/parms.php reads it into
  // $pqBuildName; build/given.php puts it in $pqBuild, or hands it to the first play when
  // the run's own source is already a store.

  return TRUE;

?>