<?php

  // ndjson - newline delimited JSON (JSON Lines): a JSON value a line, a row each, read and
  // written - what a log, a stream or a bulk export is made of. Known on sight: lines that
  // each open with { and close with }, which no one JSON document can be.

  return [
    'name'      => 'NDJSON',
    'mime'      => 'application/x-ndjson',
    'accept'    => [ 'application/x-ndjson', 'application/jsonl', 'application/jsonlines' ],
    'extension' => [ 'ndjson', 'jsonl' ],
    'shape'     => 'rows',
    'lib'       => 'ndjson',
    'read'      => fn ( $text, $options ) => padNdjsonRead ( $text ),
    'write'     => fn ( $data, $options ) => padNdjsonWrite ( padFormatRows ( $data ) ),
    'sniff'     => [ 'json', fn ( $text ) => padFormatNdjsonSniff ( $text ) ],
    'download'  => 'inline'
  ];

?>
