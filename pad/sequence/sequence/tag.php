<?php

  // Runs one complete sequence for a template tag: set up, generate, exit.
  //
  // This is where every {sequence}, {make}, {keep}, {remove}, {flag}, {pull} and {action}
  // ends up, whether the name arrived as the tag ( start/tags/ ) or as a prefixed type
  // ( start/types/ via sequence/type.php ). inits/ resolves which sequence type, stored
  // sequence or action was meant and reads the parameters, build/ generates the values,
  // exits/ applies the actions, updates the store and publishes the values as $padData [$pad].
  //
  // Returns TRUE when the run produced occurrences for the level to iterate, FALSE when it
  // came up empty, so the tag falls through to its null/else branch.

  include PQ . 'inits/tag.php';
  include PQ . 'inits/inits.php';
  include PQ . 'build/build.php';
  include PQ . 'exits/exits.php';

  if   ( count ( $padData [$pad] ) ) return TRUE;
  else                               return FALSE;

?>