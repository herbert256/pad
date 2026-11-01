<?php

  // {barcode '8712345678906'/} - a barcode as inline SVG, drawn by the codes subsystem in
  // subs/codes/: type= names the symbology, a file of subs/codes/types/ - the same code as the barcode:
  // type writes it, {barcode:ean13 '871234567890'/}. Left out, twelve or thirteen digits are an
  // EAN-13 and anything else Code 128. An EAN or UPC number may leave its check digit off: it
  // is added, and one given is checked.
  //
  // height= is the height of the bars in pixels (default 60), scale= the width of one
  // module (default 2), title= the accessible name, else the text; plain leaves the
  // digits under the bars out; color= and background= change the black on white. A
  // two-dimensional symbology - qr, datamatrix - takes size= instead of height=, scale= and
  // plain.

  $padCodeKind = strtolower ( trim ( (string) padTagParm ( 'type', preg_match ( '/^\d{12,13}$/', trim ( padParmText ( $padParm ) ) ) ? 'ean13' : 'code128' ) ) );

  return include PAD . 'subs/codes/barcode.php';

?>
