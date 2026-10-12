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

  // The text is encoded as the characters it holds: a field inside the quoted text - a
  // mailto: made from a field - travels with the stand-ins of { } | = , @ ' " \ that the
  // page's write-out turns back, and those went into the code: the @ scanned as U+E040.

  $padQrSvg = padQr ( padUnprotect ( (string) $padParm ), $padQrLevel,
                      max ( 21, (int) padTagParm ( 'size', 160 ) ),
                      (string) padTagParm ( 'title' ),
                      (string) padTagParm ( 'color', '#000' ),
                      (string) padTagParm ( 'background', '#fff' ) );

  if ( $padQrSvg === '' and $padCheckSyntax )
    padError ( 'the text is too long for a qr code - ' . strlen ( (string) $padParm ) . ' bytes, at level ' . $padQrLevel );

  return $padQrSvg;

?>
