<?php

  // A regex search with ^ or $ finds every line that matches: the file was matched as a whole
  // first, without m, so ^ and $ held at the file's start and end only and the hello
  // application's <h1>{$message}</h1>, on its sixth line, was not found.

  $search = fn ( $query ) => ( function ( $body ) { return include APP . '_api/search.php'; } ) ( [ 'app' => 'hello', 'query' => $query, 'regex' => TRUE ] );

  $lines  = fn ( $found ) => implode ( ',', array_map ( fn ( $hit ) => $hit ['path'] . ':' . $hit ['line'], array_filter ( $found ['hits'] ?? $found, fn ( $hit ) => $hit ['path'] == 'index.pad' ) ) );

  $shown  = 'h1>$ ' . $lines ( $search ( 'h1>$' ) );

?>
