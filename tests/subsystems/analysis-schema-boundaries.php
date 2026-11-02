<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $path ) { return scandir ( $path ); }
  function padError ( $message ) { throw new InvalidArgumentException ( $message ); }
  require PAD . 'lib/subsystem.php';
  require PAD . 'lib/schemas.php';
  $checks = 0;
  function check ( $condition, $message ) { global $checks; $checks++; if ( ! $condition ) throw new RuntimeException ( $message ); }
  foreach ( [
    [ 'jsonschema', (object) [ "\xff" => 1 ], '$' ],
    [ 'jsonschema', (object) [ 'nested' => (object) [ "\xff" => 1 ] ], '$.nested' ],
    [ 'record', [ "\xff" => 1 ], '$' ],
    [ 'rowset', [ [ 'name' => 'valid' ], [ "\xff" => 1 ] ], '$[1]' ]
  ] as [ $kind, $value, $path ] ) {
    $result = padSchema ( $kind, $value, [] );
    check ( ! $result ['valid'] && $result ['issues'] [0] ['code'] === 'encoding' && $result ['issues'] [0] ['path'] === $path, 'invalid key rejected at parent path' );
    check ( json_encode ( $result ) !== FALSE, 'issue does not reproduce the invalid UTF-8 property name' );
  }
  check ( padSchema ( 'jsonschema', (object) [ 'café' => 1, '0' => 2, '' => 3 ], [] ) ['valid'], 'Unicode, numeric and empty property names remain valid' );
  require PAD . 'subs/ai/_lib/json.php';
  check ( padAiJsonValueError ( (object) [ "\xff" => 1 ], [] ) === '', 'extracted AI compatibility validator retains its original behavior' );
  echo "analysis schema boundaries: $checks assertions passed\n";
?>
