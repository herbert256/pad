<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padUnprotectQuotes ( $value ) { return $value; }
  require PAD . 'subs/codes/_lib/rmqr.php';
  require PAD . 'subs/codes/_lib/maxicode.php';
  require PAD . 'subs/codes/_lib/barcode.php';
  $fixtures = json_decode ( file_get_contents ( __DIR__ . '/fixtures/specialized-codes-zint.json' ), TRUE, 512, JSON_THROW_ON_ERROR );
  $checks = 0;
  function check ( $value, $message ) { global $checks; $checks++; if ( ! $value ) throw new RuntimeException ( $message ); }
  foreach ( $fixtures as $i => $fixture ) {
    $encoder = $fixture ['kind'] === 'rmqr' ? 'padRmqrEncode' : 'padMaxicodeEncode';
    $code = $encoder ( $fixture ['text'], $fixture ['options'] );
    check ( is_array ( $code ), "fixture $i rejected" );
    check ( array_map ( fn ( $row ) => implode ( '', $row ), $code ['matrix'] ) === $fixture ['matrix'], "fixture $i differs from Zint" );
    if ( $fixture ['capacity'] ?? FALSE ) check ( is_string ( $encoder ( $fixture ['text'] . $fixture ['text'] [0], $fixture ['options'] ) ), "capacity fixture $i accepted excess data" );
  }
  foreach ( [ [ '', [] ], [ 'hello', [ 'level' => 'L' ] ], [ 'hello', [ 'mode' => 'kanji' ] ], [ '1', [ 'version' => 'R8x43' ] ], [ 'a', [ 'mode' => 'alnum' ] ], [ '漢字', [] ], [ "\xff", [] ], [ 'x', [ 'eci' => 26 ] ] ] as [ $text, $options ] ) check ( is_string ( padRmqrEncode ( $text, $options ) ), 'invalid rMQR accepted' );
  foreach ( [ [ '', [] ], [ 'x', [ 'mode' => 6 ] ], [ 'x', [ 'mode' => 2 ] ], [ 'x', [ 'mode' => 3, 'postcode' => 'lower', 'country' => 528, 'service' => 1 ] ], [ 'x', [ 'mode' => 2, 'postcode' => '1234567890', 'country' => 528, 'service' => 1 ] ], [ 'x', [ 'postcode' => '123' ] ], [ '漢字', [] ], [ "\xff", [] ], [ 'x', [ 'encoding' => 'SJIS' ] ] ] as [ $text, $options ] ) check ( is_string ( padMaxicodeEncode ( $text, $options ) ), 'invalid MaxiCode accepted' );
  $code = padMaxicodeEncode ( 'hello world' ); check ( is_array ( $code ) && $code ['usedCodewords'] === 21, 'MaxiCode single shifts failed' );
  $svg = padMaxicodeSvg ( $code, 320, '<script>alert(1)</script>', '" onload="x', 'white' );
  check ( substr_count ( $svg, '<circle ' ) === 3 && str_contains ( $svg, 'L' ) && ! str_contains ( $svg, '<script' ) && ! str_contains ( $svg, ' onload="' ), 'MaxiCode SVG geometry or escaping failed' );
  $rmqr = padRmqrEncode ( 'abc' ); $svg = padCodeMatrix ( $rmqr ['matrix'], 2, 320, 'rMQR', '#000', '#fff', 'pad-rmqr' );
  check ( str_contains ( $svg, 'viewBox="0 0 ' . ( count ( $rmqr ['matrix'] [0] ) + 4 ) . ' ' . ( count ( $rmqr ['matrix'] ) + 4 ) . '"' ), 'rMQR quiet zone mismatch' );
  echo "specialized codes: $checks checks; " . count ( $fixtures ) . " independent Zint matrices, capacities, invalid inputs and native SVG passed\n";
?>
