<?php

  // A regex search with ^ or $ finds every line that matches: the file was matched as a whole
  // first, without m, so ^ and $ held at the file's start and end only and the <title> of the
  // hello application's _inits.pad, on its fifth line of eight, was not found.

  $search = fn ( $query ) => ( function ( $body ) { return include APP . '_api/search.php'; } ) ( [ 'app' => 'hello', 'query' => $query, 'regex' => TRUE ] );

  $lines  = fn ( $found ) => implode ( ',', array_map ( fn ( $hit ) => $hit ['path'] . ':' . $hit ['line'], array_filter ( $found ['hits'] ?? $found, fn ( $hit ) => $hit ['path'] == '_inits.pad' ) ) );

  $shown  = 'title>$ ' . $lines ( $search ( 'title>$' ) );

?>
