<?php

  // Build strategy 'build': the type's own build.php returns its whole term list, computed
  // in one go from the current parameters. Puts it in $pqFixed and hands it to the fixed
  // iterator.
  //
  // A range whose one action totals the terms, with nothing between the range and the action
  // that would pass a term over or change it, asks for a plan instead of the list - see
  // build/total/ask.php for when, build/total/make.php for the total worked out from the plan,
  // and padGetRange () - so the run takes no memory that grows with the range.

  $pqTotalPlan = ( $pqSeq == 'range' and include PQ . 'build/total/ask.php' );

  $pqFixed = include PT . "$pqSeq/build.php";

  // The plan is asked for here alone: a play of range, made by the same build.php, takes the
  // list. A range that is no plan - letters, fractions, one value - comes back as the list.

  $pqTotalMade = ( $pqTotalPlan and is_array ( $pqFixed ) and isset ( $pqFixed ['step'] ) and array_key_exists ( 'first', $pqFixed ) );
  $pqTotalPlan = FALSE;

  if ( $pqTotalMade ) {
    include PQ . 'build/total/make.php';
    return;
  }

  include PQ . 'build/types/type/fixed.php';

?>
