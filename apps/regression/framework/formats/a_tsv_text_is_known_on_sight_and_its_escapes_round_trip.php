<?php

  // TSV has no quoting: a tab, a newline and a backslash in a cell are written \t, \n and
  // \\, and read back as what they were.

  $tsvRows = [ [ 'name' => "tab\there", 'note' => "two\nlines" ], [ 'name' => 'back\\slash', 'note' => 3 ] ];
  $tsvText = padFormatWrite ( 'tsv', $tsvRows );

  $tsvShow = json_encode ( $tsvText );
  $tsvBack = ( padFormatRead ( 'tsv', $tsvText ) == $tsvRows ) ? 'read back the same' : 'read back otherwise';

?>
