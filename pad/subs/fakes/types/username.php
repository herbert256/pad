<?php

  // username - A login name made of a name, emma.walker, ewalker or emma_walker42 - in a row
  // with a name before it that name, else a name of its own.

  $person = explode ( ' ', strtolower ( padFakeInRow ( 'name' ) ?? padFakeName () ), 2 ) + [ 1 => 'user' ];
  $first  = preg_replace ( '/[^a-z0-9]+/', '', $person [0] ) ?: 'user';
  $last   = preg_replace ( '/[^a-z0-9]+/', '', $person [1] ) ?: 'user';

  return match ( padFakeNumber ( 0, 2 ) ) {
    0       => "$first.$last",
    1       => $first [0] . $last,
    default => "{$first}_{$last}" . padFakeNumber ( 1, 99 ),
  };

?>
