<?php

  // With the strict check off, the faults these readers met as PHP errors are no rows as any
  // other fault of theirs is: an INI key named like a later section, arrays and inline
  // tables nested 200000 deep, a GeoJSON geometry whose type is a list - where the request
  // ended on a TypeError, PHP's stack or "Array to string conversion".

  $padCheckSyntax = FALSE;

  $lenientRead = implode ( ' ', array_map ( fn ( $one ) => '[' . count ( padFormatRead ( $one [0], $one [1] ) ) . ']', [
    [ 'ini',     "a = 1\n[a]\nb = 2" ],
    [ 'toml',    'a = ' . str_repeat ( '[', 200000 ) . str_repeat ( ']', 200000 ) ],
    [ 'toml',    'a = ' . str_repeat ( '{b = ', 100000 ) . '1' . str_repeat ( '}', 100000 ) ],
    [ 'geojson', '{"type":"Feature","geometry":{"type":["x"]},"properties":{}}' ],
    [ 'toml',    'a = ' . str_repeat ( '[', 512 ) . str_repeat ( ']', 512 ) ]
  ] ) );

?>
