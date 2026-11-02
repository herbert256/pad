<?php

  // BM25 by hand: three rows, apple in two. N 3, df 2: idf = ln(1 + (3 - 2 + 0.5) / (2 + 0.5))
  // = ln 1.6 = 0.470. The lengths are 2, 3 and 1 terms, 2 on average; k1 1.2, b 0.75.
  //   red apple:       tf' = 1 / (0.25 + 0.75 * 2/2) = 1      -> 0.470 * 1 * 2.2 / 2.2 = 0.470
  //   green apple pie: tf' = 1 / (0.25 + 0.75 * 3/2) = 0.7273 -> 0.470 * 1.6 / 1.9273 = 0.390
  // A boost of 2 on the field doubles tf' before the saturation: red apple 0.470 * 2 * 2.2 /
  // 3.2 = 0.646.

  $fruit = [ [ 't' => 'Red apple' ], [ 't' => 'Green apple pie' ], [ 't' => 'Banana' ] ];

  $found = [];

  foreach ( padSearch ( $fruit, 'apple', [ 'name' => 'fruit' ] ) as $row )
    $found [] = $row ['t'] . '=' . $row ['score'];

  foreach ( padSearch ( $fruit, 'apples', [ 'name' => 'fruit', 'fields' => 't^2' ] ) as $row )
    $found [] = $row ['t'] . '=' . $row ['score'];

  $found [] = count ( padSearch ( $fruit, 'cherry', [ 'name' => 'fruit' ] ) );

  $found = implode ( ', ', $found );

?>
