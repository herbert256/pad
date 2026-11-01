<?php

  // A regex search with ^ or $ finds every line that matches: the file was matched as a whole
  // first, without m, so ^ and $ held at the file's start and end only and the <title> of the
  // frame every application shares, _common's _tags/padFrame.pad, on its sixth line of many,
  // was not found.

  $search = fn ( $query ) => ( function ( $body ) { return include APP . '_api/search.php'; } ) ( [ 'app' => '_common', 'query' => $query, 'regex' => TRUE ] );

  $lines  = fn ( $found ) => implode ( ',', array_map ( fn ( $hit ) => $hit ['path'] . ':' . $hit ['line'], array_filter ( $found ['hits'] ?? $found, fn ( $hit ) => $hit ['path'] == '_tags/padFrame.pad' ) ) );

  $shown  = 'title>$ ' . $lines ( $search ( 'title>$' ) );

?>
