<?php

  // qr - a QR code (ISO/IEC 18004), the code a phone camera reads: any text - digits, the 45
  // characters of the alphanumeric mode, else the bytes of its UTF-8 - in the smallest of the
  // 40 versions that holds it, under the mask with the lowest penalty (subs/codes/_lib/qr.php).
  // The {qr} tag is this symbology by a name of its own. Options: size= the width and height
  // in pixels, the quiet zone of four modules included (160 unless given, at least 21);
  // level= the error correction, L, M (default), Q or H; title=, color=, background=.

  $padQrLevel = strtoupper ( (string) padTagParm ( 'level', 'M' ) );

  if ( ! in_array ( $padQrLevel, [ 'L', 'M', 'Q', 'H' ] ) ) {
    padCodeFail ( "the qr code has no level '" . padMakeSafe ( $padQrLevel, 10 ) . "' - L, M, Q or H" );
    $padQrLevel = 'M';
  }

  // The text is encoded as the characters it holds (padCodeText): a field inside the quoted
  // text - a mailto: made from a field - travels with the stand-ins of { } | = , @ ' " \ and
  // arrives HTML-escaped, and those went into the code: the @ scanned as U+E040, a & as &amp;.
  // A code of nothing scans as nothing and has no name to be heard by: an empty text drew a
  // symbol of zero bytes with an empty title, without a word.

  $padQrText = padCodeText ( FALSE );

  if ( $padQrText === '' ) {
    foreach ( [ 'size', 'title', 'color', 'background' ] as $padQrOption )
      padTagParm ( $padQrOption );
    return padCodeFail ( "the qr code has no text - {qr 'https://example.com'/}" );
  }

  $padQrSvg = padQr ( $padQrText, $padQrLevel,
                      max ( 21, (int) padTagParm ( 'size', 160 ) ),
                      padParmText ( padTagParm ( 'title' ) ),
                      (string) padTagParm ( 'color', '#000' ),
                      (string) padTagParm ( 'background', '#fff' ) );

  if ( $padQrSvg === '' )
    return padCodeFail ( 'the text is too long for a qr code - ' . strlen ( $padQrText ) . ' bytes, at level ' . $padQrLevel );

  return $padQrSvg;

?>
