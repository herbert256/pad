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
  // pages off again and books the page for a {pager} with the number of terms there are. Rows
  // were every term of a run with an end of its own, {sequence '1..30', page=2} showed all
  // thirty, page=0 and page='x' ended the page under the strict check where they are a data
  // tag's first page, and {pager 'sequence'} found no paged tag.
  //
  // A range, a list, a store or a type made in one go, where nothing can turn a term down - no
  // play, unique, min=, max=, stop=, try=, randomly= or random increment, no parameter drawn or
  // read from a store per term - is made up to the end of the page shown, and exits/page.php
  // counts the terms of the rest of the list for the pager without making them,
  // $pqPageCount. Any other run is made up to the end of the page after the one shown, so the
  // pager has a next page while there are more terms: the pager of such a run knows the pages
  // made. Every term of the list was made to count them: {sequence '1..100000', page=2,
  // rows=10} took five to nine seconds where it takes a tenth, '1..1000000' a minute, and with a
  // play the five seconds a run has cut the walk, so the pager of {sequence '1..100000', keep,
  // even, page=2, rows=10} ended at page 1758 of 5000.

  $pqPageSkip  = 0;
  $pqPageSize  = NULL;
  $pqPageCount = FALSE;
  $pqPageWhole = NULL;

  // A page past the integer range is a page past the end, as page=1e30 written out is, and
  // one below it the first page: cast, page=$n with $n 10 ** 30 ended the page on "The float
  // 1.0E+30 is not representable as an int". The terms before the page are counted in a float
  // and kept below the range for the same reason.

  if ( $pqPage !== '' ) {

    if ( is_numeric ( $pqPage ) and ( $pqPage >= PHP_INT_MAX or $pqPage <= PHP_INT_MIN ) )
      $pqPage = ( $pqPage > 0 ) ? PHP_INT_MAX : 1;

    $pqPage     = is_scalar ( $pqPage ) ? max ( 1, (int) $pqPage ) : 1;
    $pqPageSize = $pqRowsGiven ? $pqRows : 10;
    $pqPageSkip = ( ( $pqPage - 1 ) * (float) $pqPageSize >= PHP_INT_MAX ) ? PHP_INT_MAX : ( $pqPage - 1 ) * $pqPageSize;

    $pqPageCount = ( ( $pqPull or in_array ( $pqBuild, [ 'build', 'given', 'fixed' ] ) )
                     and ! count ( $pqPlays ) and ! $pqUnique and ! $pqRandomly and ! $pqRandomInc
                     and $pqMin === PHP_INT_MIN and $pqMax === PHP_INT_MAX and $pqStop === PHP_INT_MAX
                     and ! isset ( $pqParms ['try'] )
                     and ! ( is_string ( $pqParm ) and $pqParm !== ''
                             and ( ( str_contains ( $pqParm, '..' ) and $pqSeq != 'range' ) or isset ( $pqStore [$pqParm] ) ) ) );

    $pqPageEnd = $pqPageCount ? $pqPage : $pqPage + 1;
    $pqRows    = ( $pqPageEnd * (float) $pqPageSize >= PHP_INT_MAX ) ? PHP_INT_MAX : $pqPageEnd * $pqPageSize;

    // The run an action written before page= acts on (inits/page.php): every term of a run
    // with an end of its own, as many as the page's of any other.

    $pqPageWhole = ( $pqPull or $pqStop != PHP_INT_MAX or $pqTo != PHP_INT_MAX or in_array ( $pqBuild, [ 'build', 'given', 'fixed' ] ) )
                 ? PHP_INT_MAX : $pqRows;

  }

?>
