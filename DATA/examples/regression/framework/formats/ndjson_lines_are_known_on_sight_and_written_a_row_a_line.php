<?php

  // NDJSON written: a row a line, as JSON writes it - the slashes and the characters past
  // ASCII as they are - and read again the same rows.

  $ndRows = [ [ 'path' => '/a/b', 'name' => 'Zoë' ], [ 'path' => '/c', 'tags' => [ 'x', 'y' ] ] ];
  $ndText = padFormatWrite ( 'ndjson', $ndRows );

  $ndShow = str_replace ( "\n", ' | ', trim ( $ndText ) );
  $ndBack = ( padFormatRead ( 'jsonl', $ndText ) === $ndRows ) ? 'read back the same' : 'read back otherwise';

?>
