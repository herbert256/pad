<?php

  // Marker file, never included: its existence is what makes 'pull' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // pull='name' takes the terms from a sequence stored earlier under that name instead of
  // generating any: inits/parms.php reads it into $pqPull, build/inits.php switches $pqBuild
  // to 'pull', and build/types/pull.php lifts $pqStore[$pqPull] into $pqFixed so that
  // from/to, the plays and the actions apply to the stored terms. A bare pull - and the
  // {pull} tag, the pull: prefix and {resume} - picks up the last pushed store, $padLastPush.

  return TRUE;

?>