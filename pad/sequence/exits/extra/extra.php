<?php

  // Extra stage of the wind-down: the rows now exist, so decorate them with the columns that
  // say how each term came about - the original generated value, the value pulled from a
  // store, the play results, the action results and the columns an earlier run in the chain
  // left behind - then record the finished rows for the next run to chain onto.
  //
  // Every step here adds fields to $padData[$pad]; none changes the number of rows.
  //
  // $pqRowAt says which term of the build each row is - its position in $pqOrgHit,
  // $pqPlaysHit and the pulled store - or FALSE for a row that is none. Without an action
  // the rows are the terms in order; an action reorders, cuts or collapses them, and a term
  // it kept still has the 'x' key actions/assoc.php gave it and the value the build gave it,
  // where a total, a store's value or a repeat has neither. Read by position, the columns
  // gave the rows the terms in the order they were made: {fibonacci rows=6, reverse}
  // printed {$fibonacci} 0 1 1 2 3 5 beside a {$sequence} of 5 3 2 1 1 0, and with sum the
  // first term, 0, for the total 12.

  $pqRowAt = [];

  foreach ( $pqResult as $padK => $padV )
    if ( ! count ( $pqActions ) )
      $pqRowAt [] = $padK;
    elseif ( is_string ( $padK ) and str_starts_with ( $padK, 'x' )
             and array_key_exists ( (int) substr ( $padK, 1 ), $pqResultBuilt )
             and $pqResultBuilt [ (int) substr ( $padK, 1 ) ] === $padV )
      $pqRowAt [] = (int) substr ( $padK, 1 );
    else
      $pqRowAt [] = FALSE;

  include PQ . 'exits/extra/org.php';
  include PQ . 'exits/extra/pull.php';
  include PQ . 'exits/extra/plays.php';
  include PQ . 'exits/extra/actions.php';
  include PQ . 'exits/extra/chain.php';
  include PQ . 'exits/extra/set.php';

?>
