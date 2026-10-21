<?php

  // Starts one occurrence - one pass of the current level over one row of its data.
  //
  // Opens the row (init), publishes its fields as variables (set) and runs the app's row
  // callback, unless a before= callback already walked the whole set up front. Reached
  // from level/end.php for every following row, and from build/build.php,
  // start/pad/code.php and level/start.php for the first one.

  include PAD . 'occurrence/init.php';

  if ( $padInfo )
    include PAD . 'events/occurStart.php';

  include PAD . 'occurrence/set.php';

  // The row phase reads the row's fields as plain variables, which holds where the level loop
  // runs at global scope. A pass inside a PHP function - padCode, padSandbox, a _data file, a
  // | code pipe - runs it in the function's scope, where the fields are globals it never
  // imported: the callback ended the request on "Undefined variable". There the phase goes
  // through padCallbackBeforeXxx, which lifts the application's globals in and back out.

  // The @else@ branch of an empty level is no row of its data, so the row phase does not run
  // for it - init and exit still do: a callback reading a field of its row ended the request.
  // Neither is the one stand-in row an @start@ prelude or an @end@ coda renders on, which
  // level/start_end/ marks with $padSectionRows: the phase ran five times for three rows, and
  // one reading $n ended the request.

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) and ! $padElse [$pad]
       and ! isset ( $padSectionRows [$pad] ) )
    if ( $GLOBALS ['padStrFunCnt'] ?? 0 )
      padCallbackBeforeXxx ( 'row' );
    else
      include PAD . 'callback/row.php' ;

?>
