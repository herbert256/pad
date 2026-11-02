<?php

  // psv - pipe-separated records, with CSV-style quoting and a header of field names.
  // Pipes and newlines in cells are quoted; a quote is doubled. Selected explicitly or
  // by .psv, never guessed from pipes in prose or Markdown tables.
  return [
    'name' => 'PSV',
    'mime' => 'text/plain',
    'accept' => [],
    'extension' => [ 'psv' ],
    'shape' => 'rows',
    'lib' => 'psv',
    'raw' => TRUE,
    'read' => fn ( $text, $options ) => padPsvRead ( $text ),
    'write' => fn ( $data, $options ) => padPsvWrite ( padFormatRows ( $data ) ),
    'download' => 'attachment'
  ];

?>
