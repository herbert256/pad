<?php

  // json - JSON (RFC 8259), read and written: the reader is the engine's own, data/json.php,
  // which takes the document out of the text around it; the writer writes any value as a
  // JSON page answer has it - pretty, slashes and characters past ASCII as they are. A page
  // answer in JSON (?page&padFormat=json) is one object, keyed by the names $padExpose gives.

  return [
    'name'      => 'JSON',
    'mime'      => 'application/json',
    'extension' => [ 'json' ],
    'shape'     => 'data',
    'lib'       => 'data',
    'read'      => fn ( $text, $options ) => padFormatDataRead ( 'json', $text ),
    'write'     => fn ( $data, $options ) => padFormatJsonWrite ( $data, $options )
  ];

?>
