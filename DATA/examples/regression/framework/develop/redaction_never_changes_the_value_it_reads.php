<?php

  // A report, the toolbar and {debug} show a value with its secrets redacted - redacting a
  // copy. An array an application walked with foreach ( ... as &$row ) keeps its rows as
  // references, and the redaction wrote through them: the page went on with its own data
  // redacted. And a global array that holds itself - $loop ['self'] = &$loop - was walked
  // round and round until the call stack ran out, taking the report of the real error with it.

  $rows = [ [ 'user' => 'bob', 'password' => 'kept' ] ];

  foreach ( $rows as &$row )
    $row ['user'] = ucfirst ( $row ['user'] );

  $shown = padRedact ( $rows );

  $loop = [ 'a' => 1 ];
  $loop ['self'] = &$loop;

  $deep = padRedact ( $loop );

  for ( $levels = 0; is_array ( $deep ); $levels++ )
    $deep = $deep ['self'];

  $result = 'shown: ' . $shown [0] ['password'] . ', the row itself: ' . $rows [0] ['password']
          . ', a loop ends ' . ( $levels < 100 ? 'soon' : 'LATE' ) . ' with: ' . $deep;

?>
