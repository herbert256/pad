<?php

  // Settles the two safety limits: $pqRows, how many values to keep, and $pqTry, how many
  // candidates may be tested to find them.
  //
  // A run that already has a natural end - it pulls a store, names a stop or to value, or
  // builds from a fixed or given array - is let run unbounded. Everything else falls back to
  // the configured $padSeqDefaultRows / $padSeqDefaultTries, so an open-ended generator such
  // as {prime} cannot spin forever.

  // A pulled store and a fixed or given array end with their data, so their candidates are
  // not counted. A given array - the values an action is handed, sequence:sum([..]) - is
  // all of its rows too: it saw its first ten values only, and the sum of 1 to 12 was 55. A
  // fixed type's list keeps the default count, as any type does. A stop or to value is a
  // natural end only when it is reached: those runs, an explicit try= and every other run
  // stop at $padSeqMaxTries candidates at the most.

  // The build= override comes first: the limits are those of the strategy that runs. Applied
  // after them, build='bool' on a range kept the unbounded count of a list and walked the
  // values until the time a run has was up.

  include PQ . 'build/given.php';

  $pqMaxTries = $GLOBALS ['padSeqMaxTries'] ?? 1000000;
  $pqRowsGiven = ( $pqRows !== NULL and $pqRows !== '' );

  if ( $pqPull or $pqBuild == 'fixed' or $pqBuild == 'given' ) {
    if ( ! $pqTry  ) $pqTry  = PHP_INT_MAX ;
  } elseif ( $pqTry )
    $pqTry = min ( $pqTry, $pqMaxTries );

  if ( $pqStop != PHP_INT_MAX or $pqTo != PHP_INT_MAX )
    if ( ! $pqTry  ) $pqTry  = $pqMaxTries ;

  if ( $pqPull or $pqStop != PHP_INT_MAX or $pqTo != PHP_INT_MAX or in_array ( $pqBuild, [ 'build', 'given' ] ) )
    if ( $pqRows === NULL or $pqRows === '' ) $pqRows = PHP_INT_MAX ;

  if ( ! $pqTry  ) $pqTry  = $padSeqDefaultTries;

  // A count of 0 is a count: no rows. Unset - NULL - takes the default. They used to be one
  // and the same, so {sequence $n} with $n 0, {loop 0} and rows=0 all ran ten rows; the
  // engine's own 'no limit' is PHP_INT_MAX now, where it was 0 too.

  if ( $pqRows === NULL or $pqRows === '' )
    $pqRows = $padSeqDefaultRows;

  // A count past the integer range is as many as there are: cast, it ended the request on
  // "The float 1.0E+30 is not representable as an int" - {sequence $n} with $n 10 ** 30.

  if ( is_numeric ( $pqRows ) and ( $pqRows >= PHP_INT_MAX or $pqRows <= PHP_INT_MIN ) )
    $pqRows = ( $pqRows > 0 ) ? PHP_INT_MAX : 0;

  $pqRows = (int) $pqRows;

  // page= pages through the terms as the page option pages through the rows of a data tag
  // (handling/types/page.php): rows= is the page's size, 10 when it is not given, and a page
  // below 1, or one that is no number, is page 1. exits/page.php takes the terms of the other
  // pages off again and books the page for a {pager} with the number of terms there are: a
  // range, a list, a store or a type made in one go is made whole for it, any other run up to
  // the page after the one shown, so the pager has a next page while the type goes on. Rows
  // were every term of a run with an end of its own, {sequence '1..30', page=2} showed all
  // thirty, page=0 and page='x' ended the page under the strict check where they are a data
  // tag's first page, and {pager 'sequence'} found no paged tag.

  $pqPageSkip = 0;
  $pqPageSize = NULL;

  if ( $pqPage !== '' ) {

    $pqPage     = is_scalar ( $pqPage ) ? max ( 1, (int) $pqPage ) : 1;
    $pqPageSize = $pqRowsGiven ? $pqRows : 10;
    $pqPageSkip = (int) min ( ( $pqPage - 1 ) * (float) $pqPageSize, PHP_INT_MAX );

    if ( $pqPull or in_array ( $pqBuild, [ 'build', 'given', 'fixed' ] ) )
      $pqRows = PHP_INT_MAX;
    else
      $pqRows = (int) min ( ( $pqPage + 1 ) * (float) $pqPageSize, PHP_INT_MAX );

  }

?>
