<?php

  // A regex search may escape its slashes, as a regular expression in JavaScript is written:
  // <\/h1> finds what </h1> finds. Every slash was escaped once more, and \/ became \\/ - a
  // backslash, then the slash that ends the pattern: "that is not a regular expression PHP
  // takes". A slash after an escaped backslash is escaped still, and a plain search for a
  // backslash and a slash finds them as they are.

  $search = function ( $query, $regex = TRUE ) {
    try {
      $found = ( function ( $body ) { return include APP . '_api/search.php'; } ) ( [ 'app' => 'hello', 'query' => $query, 'regex' => $regex ] );
      return count ( $found ['hits'] ) . ' hits';
    } catch ( RuntimeException $e ) {
      return $e->getMessage ();
    }
  };

  $out = [];

  foreach ( [ '</h1>', '<\/h1>', '<\\\\/h1>', '[/]h1>' ] as $query )
    $out [] = "regex $query: " . $search ( $query );

  $out [] = 'plain <\/h1>: ' . $search ( '<\/h1>', FALSE );

?>
