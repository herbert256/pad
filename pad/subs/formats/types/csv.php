<?php

  // csv - comma separated values (RFC 4180), read and written: the reader is the engine's
  // own, data/csv.php - a header line naming the fields, then a row per line - and the
  // writer the one a CSV page answer is made with: a header row of every key the rows use,
  // a cell holding a list as its JSON, and a text cell a spreadsheet would run as a formula
  // (=, +, -, @ in front) with a ' before it.

  return [
    'name'      => 'CSV',
    'mime'      => 'text/csv',
    'extension' => [ 'csv' ],
    'shape'     => 'rows',
    'lib'       => 'data',
    'read'      => fn ( $text, $options ) => padFormatDataRead ( 'csv', $text ),
    'write'     => fn ( $data, $options ) => padFormatCsvWrite ( padFormatRows ( $data ) ),
    'download'  => 'inline'
  ];

?>
