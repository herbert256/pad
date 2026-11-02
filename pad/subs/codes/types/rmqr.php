<?php
  // rmqr - rectangular Micro QR, all 32 sizes, numeric/alphanumeric/byte segments.
  include_once PAD . 'subs/codes/_lib/rmqr.php';
  $rmqrText = padCodeText ( FALSE ); $rmqrOptions = [];
  foreach ( [ 'version' => 'auto', 'level' => 'M', 'mode' => 'auto', 'encoding' => 'ISO-8859-1' ] as $name => $default ) $rmqrOptions [$name] = padTagParm ( $name, $default );
  $rmqr = padRmqrEncode ( $rmqrText, $rmqrOptions );
  if ( ! is_array ( $rmqr ) ) return padCodeFail ( $rmqr );
  return padCodeMatrix ( $rmqr ['matrix'], 2, max ( 40, min ( 4096, (int) padTagParm ( 'size', 320 ) ) ), padParmText ( padTagParm ( 'title', $rmqrText ) ),
    (string) padTagParm ( 'color', '#000' ), (string) padTagParm ( 'background', '#fff' ), 'pad-rmqr' );
?>
