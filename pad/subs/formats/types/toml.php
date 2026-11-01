<?php

  // toml - TOML 1.0, read and written: tables, arrays of tables, inline tables, dotted keys,
  // every kind of string, number and date. A date stays the text it is (2026-11-01,
  // 1979-05-27T07:32:00Z) and is written back bare. Not known on sight - key = value lines
  // read as CSV before and still do: name it, format='toml', or keep it in a .toml file.

  return [
    'name'      => 'TOML',
    'mime'      => 'application/toml',
    'extension' => [ 'toml' ],
    'shape'     => 'data',
    'lib'       => 'toml',
    'read'      => fn ( $text, $options ) => padTomlRead ( $text ),
    'write'     => fn ( $data, $options ) => padTomlWrite ( $data, $options ),
    'download'  => 'inline'
  ];

?>
