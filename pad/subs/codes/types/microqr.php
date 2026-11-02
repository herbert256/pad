<?php

  // microqr - Micro QR versions M1 through M4, with explicit encoding mode, error correction and mask options.

  include_once PAD . 'subs/codes/_lib/microqr.php';
  $padMicroText = padCodeText ( FALSE );
  $padMicroOptions = [];
  foreach ( ['version'=>'auto','level'=>'auto','mode'=>'auto','mask'=>-1,'encoding'=>'UTF-8'] as $padMicroName => $padMicroDefault )
    $padMicroOptions [$padMicroName] = padTagParm ( $padMicroName, $padMicroDefault );
  if ( is_numeric ( $padMicroOptions ['mask'] ) and is_finite ( (float) $padMicroOptions ['mask'] ) and floor ( (float) $padMicroOptions ['mask'] ) == $padMicroOptions ['mask'] )
    $padMicroOptions ['mask'] = (int) $padMicroOptions ['mask'];
  $padMicroSize = max ( 15, min ( 4096, (int) padTagParm ( 'size', 128 ) ) );
  $padMicroTitle = padParmText ( padTagParm ( 'title', $padMicroText ) );
  $padMicroDark = (string) padTagParm ( 'color', '#000' ); $padMicroLight = (string) padTagParm ( 'background', '#fff' );
  $padMicroCode = padMicroQrEncode ( $padMicroText, $padMicroOptions );
  if ( ! is_array ( $padMicroCode ) ) return padCodeFail ( $padMicroCode );
  return padCodeMatrix ( $padMicroCode ['matrix'], 2, $padMicroSize, $padMicroTitle, $padMicroDark, $padMicroLight, 'pad-microqr' );
?>
