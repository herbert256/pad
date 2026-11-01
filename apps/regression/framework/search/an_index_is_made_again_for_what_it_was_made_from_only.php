<?php

  // The index of rows is signed on the fields it searches: a column it does not read - the
  // stock of a product - changes without the index being made again, where every column
  // signed it and each change of the stock made and wrote the whole index anew; a change of
  // a field searched still makes it again, and the search finds the new word. The kept
  // index is told apart from the others of this application by its field's name.

  $stock = [ [ 'stockProbeName' => 'Red apple', 'stock' => 10 ], [ 'stockProbeName' => 'Pear', 'stock' => 3 ] ];

  $kept = function () {
    foreach ( glob ( DATA . 'cache/search/' . $GLOBALS ['padApp'] . '/*/*/head.json' ) ?: [] as $head )
      if ( str_contains ( (string) file_get_contents ( $head ), '"stockProbeName"' ) )
        return dirname ( $head );
    return '';
  };

  $search = function ( $q ) use ( &$stock ) {
    return implode ( ' ', array_column ( padSearch ( $stock, $q, [ 'name' => 'stock', 'fields' => 'stockProbeName' ] ), 'stock' ) );
  };

  $was = $search ( 'apple' ) . ' ' . ( $first = $kept () );

  $stock [0] ['stock'] = 9;

  $now = [ $search ( 'apple' ), $kept () === $first ? 'the same index' : 'made again' ];

  $stock [1] ['stockProbeName'] = 'Green apple';

  $now [] = $search ( 'apple' );
  $now [] = $kept () === $first ? 'the same index' : 'made again';

  $stock = implode ( ', ', $now );

  unset ( $kept, $search, $was, $first, $now );

?>
