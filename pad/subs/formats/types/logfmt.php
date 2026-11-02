<?php

  // logfmt - Logfmt key-value records with quoted values and explicit escape handling.

  return [
    'name' => 'logfmt', 'mime' => 'text/plain', 'accept' => [],
    'extension' => [ 'logfmt' ], 'shape' => 'rows',
    'lib' => 'logfmt', 'raw' => 'exact',
    'read' => fn ( $text, $options ) => padLogfmtRead ( $text, $options ),
    'write' => fn ( $data, $options ) => padLogfmtWrite ( $data, $options )
  ];
?>
