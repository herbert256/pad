<?php

  // aztec - Aztec Code (ISO/IEC 24778), the square code of train tickets and boarding passes,
  // a bullseye in its middle: any text, in the smallest of its compact (15 by 15 to 27 by 27
  // modules) and full symbols (to 151 by 151) that holds it with the error correction asked
  // (subs/codes/_lib/aztec.php). Options: size= the width and height in pixels, a quiet zone
  // of two modules included (160 unless given, at least 19); ecc= the error correction in
  // percent of the codewords, 3 more always added (23, the standard's recommendation, unless
  // given; 5 to 90); title=, color=, background=.

  include_once PAD . 'subs/codes/_lib/aztec.php';

  $padAztecEcc = padTagParm ( 'ecc', 23 );

  if ( ! is_numeric ( $padAztecEcc ) or $padAztecEcc < 5 or $padAztecEcc > 90 ) {
    padCodeFail ( "the aztec code has no error correction '" . padMakeSafe ( (string) $padAztecEcc, 10 ) . "' - a percentage from 5 to 90" );
    $padAztecEcc = 23;
  }

  $padAztecText = padCodeText ( FALSE );

  if ( $padAztecText === '' ) {
    foreach ( [ 'size', 'title', 'color', 'background' ] as $padAztecOption )
      padTagParm ( $padAztecOption );
    return padCodeFail ( "the aztec code has no text - {barcode:aztec 'Code 2D!'/}" );
  }

  $padAztecSvg = padAztec ( $padAztecText,
                            max ( 19, (int) padTagParm ( 'size', 160 ) ),
                            padParmText ( padTagParm ( 'title' ) ),
                            (string) padTagParm ( 'color', '#000' ),
                            (string) padTagParm ( 'background', '#fff' ),
                            (float) $padAztecEcc );

  if ( $padAztecSvg === '' )
    return padCodeFail ( 'the text is too long for an aztec code - ' . strlen ( $padAztecText ) . ' bytes, with ' . $padAztecEcc . '% error correction' );

  return $padAztecSvg;

?>
