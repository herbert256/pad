<?php

  // The whole set-up half of a tag-driven sequence run, between inits/tag.php and
  // build/build.php in sequence/tag.php.
  //
  // In order: wipe the previous run's state, read the tag's parameters, work out which
  // sequence type, stored sequence or action was named, decide the variable name the values
  // will be published under, settle the build strategy, let the type initialise itself,
  // collect the plays (filters), apply the make/keep/remove/flag mode, cap the row and try
  // limits, and collect the actions.

  include PQ . 'inits/clear.php';
  include PQ . 'inits/vars.php';
  include PQ . 'inits/find/find.php';
  include PQ . 'inits/name.php';
  include PQ . 'build/inits.php';
  include PQ . 'inits/init.php';
  include PQ . 'plays/inits.php';
  include PQ . 'inits/check/check.php';
  include PQ . 'inits/limits.php';
  include PQ . 'actions/inits.php';

?>