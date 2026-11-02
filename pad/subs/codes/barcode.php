<?php

  // A barcode uses {barcode:kind 'text'/}; {barcode:auto $code/} selects EAN-13 for
  // twelve or thirteen digits and Code 128 otherwise. The caller sets $padCodeKind.
  // A symbology is a file of subs/codes/types/; its options include height=, scale=,
  // title=, plain, color= and background= for bars, or size= for two-dimensional codes.
  //
  // A text the symbology cannot encode - a wrong check digit, a character it has no bars
  // for, too long a text - is an error under the strict check, and the tag writes nothing.

  include_once PAD . 'subs/codes/_lib/barcode.php';

  if ( $padCodeKind == 'auto' )
    $padCodeKind = preg_match ( '/^\d{12,13}$/D', trim ( padParmText ( $padParm ) ) ) ? 'ean13' : 'code128';

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
