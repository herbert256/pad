<?php

  // {qr 'https://example.com'} - a QR code as inline SVG, encoded by lib/qr.php: no image
  // library, no remote service. The first parameter is the text; size= is the width and
  // height in pixels (default 160), level= the error correction - L, M (default), Q or H -
  // and title= the accessible name, else the text itself. color= and background= change the
  // dark and light colours, black on white by default: a reader needs the contrast.

  $padQrLevel = strtoupper ( (string) padTagParm ( 'level', 'M' ) );

  if ( ! in_array ( $padQrLevel, [ 'L', 'M', 'Q', 'H' ] ) ) {
    if ( $padCheckSyntax )
      padError ( "the qr code has no level '" . padMakeSafe ( $padQrLevel, 10 ) . "' - L, M, Q or H" );
    $padQrLevel = 'M';
  }

  $padQrSvg = padQr ( (string) $padParm, $padQrLevel,
                      max ( 21, (int) padTagParm ( 'size', 160 ) ),
                      (string) padTagParm ( 'title' ),
                      (string) padTagParm ( 'color', '#000' ),
                      (string) padTagParm ( 'background', '#fff' ) );

  if ( $padQrSvg === '' and $padCheckSyntax )
    padError ( 'the text is too long for a qr code - ' . strlen ( (string) $padParm ) . ' bytes, at level ' . $padQrLevel );

  return $padQrSvg;

?>
