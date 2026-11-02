<?php
  return [
    'name' => 'properties', 'mime' => 'text/x-java-properties', 'accept' => [],
    'extension' => [ 'properties' ], 'shape' => 'data',
    'lib' => 'properties', 'raw' => 'exact',
    'read' => fn ( $text, $options ) => padPropertiesRead ( $text, $options ),
    'write' => fn ( $data, $options ) => padPropertiesWrite ( $data, $options )
  ];
?>
