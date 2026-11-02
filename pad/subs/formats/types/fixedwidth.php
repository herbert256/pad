<?php

  // fixedwidth - Fixed-width records read and written with explicit column widths and alignment.

  return [
    'name' => 'fixedwidth', 'mime' => 'text/plain', 'accept' => [],
    'extension' => [ 'fw' ], 'shape' => 'rows',
    'lib' => 'fixedwidth', 'raw' => 'exact',
    'read' => fn ( $text, $options ) => padFixedwidthRead ( $text, $options ),
    'write' => fn ( $data, $options ) => padFixedwidthWrite ( $data, $options )
  ];
?>
