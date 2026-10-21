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

  if ( $pqPageCut and $pqPageSize !== NULL ) {
    $pqPageCount = FALSE;
    $pqRows      = $pqPageWhole;
  }

?>
