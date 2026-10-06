<?php

  // Implements the dump option: writes a full state dump under DATA through padDumpToDir(),
  // then clears $padDumpToDirDone so a later dump in the same request starts a fresh directory
  // instead of being folded into this one.
  //
  // Included by level/start.php and again by level/go.php once the type has run. It is listed
  // in padOptionsEnd as well, but padTagParm('dump') has marked the option done by then, so
  // the end-of-level walk skips it.

  // Through $GLOBALS, which clears the guard from whatever scope this is included in - an
  // unset() of a name made global would clear only a function's own reference to it.

  padDumpToDir ( );

  unset ( $GLOBALS ['padDumpToDirDone'] );

?>
