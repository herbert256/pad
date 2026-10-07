<?php

  // {barcode '8712345678906'} - a barcode as inline SVG, encoded by lib/barcode.php. The
  // first parameter is the number or text; type= is ean13, ean8, upca or code128 - left
  // out, twelve or thirteen digits are an EAN-13 and anything else Code 128. An EAN or UPC
  // number may leave its check digit off: it is added, and one given is checked.
  //
  // height= is the height of the bars in pixels (default 60), scale= the width of one
  // module (default 2), title= the accessible name, else the text; plain leaves the
  // digits under the bars out; color= and background= change the black on white.

  $padBarcodeText = trim ( (string) $padParm );
  $padBarcodeType = strtolower ( (string) padTagParm ( 'type', preg_match ( '/^\d{12,13}$/', $padBarcodeText ) ? 'ean13' : 'code128' ) );

  $padBarcodeSvg = padBarcode ( $padBarcodeText, $padBarcodeType,
                                max ( 10, (int) padTagParm ( 'height', 60 ) ),
                                max ( 1, min ( 10, (float) padTagParm ( 'scale', 2 ) ) ),
                                (string) padTagParm ( 'title' ),
                                (bool) padTagParm ( 'plain', FALSE ),
                                (string) padTagParm ( 'color', '#000' ),
                                (string) padTagParm ( 'background', '#fff' ),
                                $padBarcodeError );

  if ( $padBarcodeSvg === '' and $padCheckSyntax )
    padError ( $padBarcodeError );

  return $padBarcodeSvg;

?>
