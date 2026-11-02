<?php

  // Whether a range run may be totalled from the plan of the range instead of its list: TRUE
  // when the tag's one action totals the terms - count, sum, product, average, median,
  // distinct - and every term of the range reaches that action as range () makes it.
  //
  // Made as a list, {sequence:range '1..1000000', count} held the million values four times over -
  // the range, the terms the walk kept, the values before the plays and the place of each in
  // the list - some 65 MB, to answer one number. The total of a range of whole numbers follows
  // from its first value, its step and its number of values (build/total/make.php).
  //
  // Anything that passes a term over, changes it or stops the walk early asks for the list as
  // before: a play, from=, to=, sole=, skip=, try=, minimal=, maximal=, stop=, unique, randomly,
  // a random increment, page=, negative; so does more than one action, or one that does not
  // total, a pull, an expression's run, and {info}, which records the walk. rows= is the one
  // that stays: the walk stops after so many terms, and so many of the plan's are totalled.

  if ( $pqEntry != 'tag' or $padInfo or count ( $pqActions ) != 1 )
    return FALSE;

  if ( ! in_array ( $pqActions [ array_key_first ( $pqActions ) ] [0], [ 'count', 'sum', 'product', 'average', 'median', 'distinct' ] ) )
    return FALSE;

  return ( ! $pqNegative and ! $pqPageCut and $pqPageSize === NULL and ! count ( $pqPlays )
           and ! $pqRandomly and ! $pqRandomInc and ! $pqRandomParm and ! $pqParmStore
           and ! $pqUnique and ! $pqSkip and ! $pqPull and ! isset ( $pqParms ['try'] )
           and $pqFrom === 1 and $pqTo === PHP_INT_MAX and $pqSole === NULL
           and $pqMin === PHP_INT_MIN and $pqMax === PHP_INT_MAX and $pqStop === PHP_INT_MAX
           and is_int ( $pqRows ) and $pqRows >= 1 );

?>
