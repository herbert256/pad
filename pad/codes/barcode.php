<?php

  // The body of a code, whichever way it is written: {barcode '8712345678906'/} with type=
  // naming the symbology, the barcode: type - {barcode:ean13 '400638133393'/}, or a bare
  // {ean13 '400638133393'/} when nothing else has the name - and {qr 'https://example.com'/},
  // the QR code's own tag. The caller sets $padCodeKind; a symbology is a file of
  // codes/types/, and the files there are all the barcode types there are (padKinds
  // ('codes'), lib/subsystem.php). The first parameter is the text; the options are the
  // symbology's own, read by its file - the bars of a one-dimensional code take height=,
  // scale=, title=, plain, color= and background=, a two-dimensional code size=, title=,
  // color= and background=.
  //
  // A text the symbology cannot encode - a wrong check digit, a character it has no bars
  // for, too long a text - is an error under the strict check, and the tag writes nothing.

  include_once PAD . 'codes/_lib/barcode.php';

  if ( ! padKind ( 'codes', $padCodeKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no barcode type '" . padMakeSafe ( $padCodeKind, 20 ) . "' - " . padCodeList () );
    return '';
  }

  // The symbology drawn goes on the xref record, which the reference application shows: the
  // pages that draw a code of each kind.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'barcode', $padCodeKind );

  return padCodeTag ( $padCodeKind, $padParm );

?>
