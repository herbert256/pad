<?php

  // Extra stage of the wind-down: the rows now exist, so decorate them with the columns that
  // say how each term came about - the original generated value, the value pulled from a
  // store, the play results, the action results and the columns an earlier run in the chain
  // left behind - then record the finished rows for the next run to chain onto.
  //
  // Every step here adds fields to $padData[$pad]; none changes the number of rows.

  include PQ . 'exits/extra/org.php';
  include PQ . 'exits/extra/pull.php';
  include PQ . 'exits/extra/plays.php';
  include PQ . 'exits/extra/actions.php';
  include PQ . 'exits/extra/chain.php';
  include PQ . 'exits/extra/set.php';

?>