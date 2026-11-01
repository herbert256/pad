<?php

  // pdf417 - PDF417 (ISO/IEC 15438), the stacked code of boarding passes, driving licences and
  // shipping documents: any text in rows of codewords between start and stop patterns, a row
  // indicator at both ends, in text, byte and numeric compaction (subs/codes/_lib/pdf417.php).
  // Options: level= the error correction, 0 to 8 - 2^(level+1) codewords - by default the
  // standard's recommendation for the size of the data; columns= the data columns, 1 to 30,
  // by default those that make the symbol about three times as wide as high; scale= the width
  // of a module in pixels (2 unless given, 1 to 10), a row three modules high and a quiet zone
  // of two; title=, color=, background=.

  include_once PAD . 'subs/codes/_lib/pdf417.php';

  $padPdf417Level   = padTagParm ( 'level', NULL );
  $padPdf417Columns = padTagParm ( 'columns', NULL );

  if ( $padPdf417Level !== NULL and ! in_array ( (string) $padPdf417Level, [ '0', '1', '2', '3', '4', '5', '6', '7', '8' ], TRUE ) ) {
    padCodeFail ( "the pdf417 code has no level '" . padMakeSafe ( (string) $padPdf417Level, 10 ) . "' - 0 to 8" );
    $padPdf417Level = NULL;
  }

  if ( $padPdf417Columns !== NULL and ( ! ctype_digit ( (string) $padPdf417Columns ) or $padPdf417Columns < 1 or $padPdf417Columns > 30 ) ) {
    padCodeFail ( "the pdf417 code has no columns '" . padMakeSafe ( (string) $padPdf417Columns, 10 ) . "' - 1 to 30" );
    $padPdf417Columns = NULL;
  }

  $padPdf417Text = padCodeText ( FALSE );

  if ( $padPdf417Text === '' ) {
    foreach ( [ 'scale', 'title', 'color', 'background' ] as $padPdf417Option )
      padTagParm ( $padPdf417Option );
    return padCodeFail ( "the pdf417 code has no text - {pdf417 'M1DOE/JOHN'/}" );
  }

  $padPdf417Svg = padPdf417 ( $padPdf417Text,
                              max ( 1, min ( 10, (float) padTagParm ( 'scale', 2 ) ) ),
                              padParmText ( padTagParm ( 'title' ) ),
                              (string) padTagParm ( 'color', '#000' ),
                              (string) padTagParm ( 'background', '#fff' ),
                              $padPdf417Level === NULL ? NULL : (int) $padPdf417Level,
                              $padPdf417Columns === NULL ? NULL : (int) $padPdf417Columns );

  if ( $padPdf417Svg === '' )
    return padCodeFail ( 'the text is too long for a pdf417 code - ' . strlen ( $padPdf417Text ) . ' bytes'
                       . ( $padPdf417Level === NULL ? '' : ", at level $padPdf417Level" )
                       . ( $padPdf417Columns === NULL ? '' : " in $padPdf417Columns columns" ) );

  return $padPdf417Svg;

?>
