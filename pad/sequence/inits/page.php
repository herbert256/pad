<?php

  // page= pages through the terms in the order the options are written, as the page option
  // of a data tag does among the handling options: an action written before page= acts on
  // every term, and the page is cut from what it left - {sequence '5;3;9;1;7;2', sort, page=1,
  // rows=3} is 1 2 3, the sum of '1..30' before page= is 465, and the pager counts the terms
  // first=5 or dedup left. The page was cut before every action, wherever it was written: the
  // first three values sorted, 3 5 9, a reverse of each page in itself, the sum of the page,
  // and a pager of every term.
  //
  // Such a run is made whole, as without page=, and its terms are not counted ahead
  // (inits/limits.php, $pqPageWhole); actions/actions.php cuts the page where page= stands
  // among the actions, $pqPageCut (actions/inits.php). A page= written before every action
  // cuts first, as before (exits/exits.php).
  //
  // A run with no end of its own - a type without to= or stop= - has no every term for an
  // action to act on: made as many terms as the page's, an action that reads the whole run -
  // reverse, shuffle, sort, last= - acted on (page+1)*rows terms, and the page was cut from
  // their reversal: every page of {sequence 'prime', reverse, page=N, rows=5} showed 29 23 19
  // 17 13, while the pager offered a next one. Such a run is cut first, wherever page= is
  // written, as it always was: page 4 is 71 67 61 59 53, the page's terms reversed.
  //
  // Not when every action before page= keeps a front of the run, in its order - first=,
  // shift, element=, prepend: of the run's first terms they make the first terms of what they
  // make of every term, so the (page+1)*rows terms made are enough and the cut stays where it
  // is written. Cut first, {sequence 'prime', first=20, page=5, rows=5} showed 73 79 83 89 97,
  // primes past the twenty, and a pager without an end.
  //
  // dedup, intersection, onlyNow and any of them under negative keep a front in order too,
  // but they drop terms of it: of the (page+1)*rows terms made fewer remain, the page came
  // short or empty and the pager, counting the pages made, ended while the run goes on -
  // {sequence 'golomb', dedup, page=2, rows=3} was 4 5 with no next page, {sequence 'prime',
  // first=3, negative, page=1, rows=3} one page. Cut first instead, the page was one of the
  // terms before the actions, page 2's terms deduplicated, 3 4. Such a run is made again with
  // twice the terms until what the actions leave in front of the cut fills the page after the
  // one shown (exits/grow.php), $pqPageGrow, and the state the build starts from is kept for
  // it here, $pqPageGrowState.

  $pqPageGrow = FALSE;

  if ( $pqPageWhole !== PHP_INT_MAX )
    foreach ( array_slice ( $pqActions, 0, $pqPageCut ) as [ $pqPageAction ] )
      if ( ! in_array ( $pqPageAction, [ 'first', 'shift', 'element', 'prepend', 'dedup', 'intersection', 'onlyNow' ] ) )
        $pqPageCut = 0;
      elseif ( $pqNegative or ! in_array ( $pqPageAction, [ 'first', 'shift', 'element', 'prepend' ] ) )
        $pqPageGrow = TRUE;

  if ( $pqPageCut and $pqPageSize !== NULL ) {
    $pqPageCount = FALSE;
    $pqRows      = $pqPageWhole;
  }

  if ( $pqPageGrow and $pqPageCut and $pqPageSize !== NULL and $pqEntry == 'tag' ) {

    $pqPageGrowNeed  = $pqRows;
    $pqPageGrowLast  = -1;
    $pqPageGrowState = [];

    // Every pq name but the ones inits/clear.php spares - the stores, how the run started,
    // a direct caller's values - and the growth's own.

    foreach ( preg_grep ( '/^pq(?!Store$|Entry$|Set|PageGrow)/', array_keys ( $GLOBALS ) ) as $pqPageGrowName )
      $pqPageGrowState [$pqPageGrowName] = $GLOBALS [$pqPageGrowName];

  } else

    $pqPageGrow = FALSE;

?>
