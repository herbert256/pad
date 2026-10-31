<?php

  // datamatrix - Data Matrix ECC 200 (ISO/IEC 16022), the square code of parts, letters and
  // medicine packs: any text, in the smallest of the 24 square symbols that holds it - 10 by
  // 10 modules up to 144 by 144, some 3,100 digits or 1,550 letters. Options: size= the width
  // and height in pixels, the quiet zone of two modules included (160 unless given, at least
  // 14); title=, color=, background=.

  include_once PAD . 'codes/_lib/datamatrix.php';

  $padDataMatrixText = padCodeText ( FALSE );

  if ( $padDataMatrixText === '' ) {
    foreach ( [ 'size', 'title', 'color', 'background' ] as $padDataMatrixOption )
      padTagParm ( $padDataMatrixOption );
    return padCodeFail ( "the data matrix code has no text - {datamatrix 'PAD-2026-0042'/}" );
  }

  $padDataMatrixSvg = padDataMatrix ( $padDataMatrixText,
                                      max ( 14, (int) padTagParm ( 'size', 160 ) ),
                                      padParmText ( padTagParm ( 'title' ) ),
                                      (string) padTagParm ( 'color', '#000' ),
                                      (string) padTagParm ( 'background', '#fff' ) );

  if ( $padDataMatrixSvg === '' )
    return padCodeFail ( 'the text is too long for a data matrix code - ' . strlen ( $padDataMatrixText ) . ' bytes' );

  return $padDataMatrixSvg;

?>
