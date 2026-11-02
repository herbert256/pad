<?php

  // Without fields= the fields searched are those that hold text in any row - a db() result
  // with NULL in its first row, a book titled 1984 - where only the first row was read and
  // its NULL or number left that field unsearched in every row. Rows without any are no
  // error: an empty result finds nothing.

  $books = [ [ 'name' => '1984', 'about' => NULL ], [ 'name' => 'Dune', 'about' => 'desert planet spice' ] ];

  $found = [];

  foreach ( [ 'spice', 'dune', '1984' ] as $q )
    $found [] = "$q: " . implode ( ' ', array_column ( padSearch ( $books, $q, [ 'name' => 'guessed' ] ), 'name' ) );

  $found [] = 'none: ' . count ( padSearch ( [], 'dune', [ 'name' => 'guessed' ] ) );

  $found = implode ( ' | ', $found );

?>
