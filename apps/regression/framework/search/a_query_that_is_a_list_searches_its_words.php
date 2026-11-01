<?php

  // A query that comes as a list - ?q[]=dune&q[]=spice through padRequest ( 'q', '' ) - is
  // searched for its words, where the list met a (string) cast and the request ended in
  // "Array to string conversion"; a query that is no text nor a list finds nothing.

  $books = [ [ 't' => 'Dune' ], [ 't' => 'Spice and wolf' ], [ 't' => 'Neuromancer' ] ];

  $found = implode ( ' ', array_column ( padSearch ( $books, [ 'dune', [ 'spice' ] ], [ 'name' => 'books' ] ), 't' ) ) . ' | '
         . count ( padSearch ( $books, new stdClass, [ 'name' => 'books' ] ) );

?>
