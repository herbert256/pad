<?php

  // {qr 'https://example.com'/} - a QR code as inline SVG, the qr symbology of the codes
  // subsystem in codes/ (codes/types/qr.php): no image library, no remote service - the same
  // code as {barcode:qr 'https://example.com'/} writes. The first parameter is the text;
  // size= is the width and height in pixels (default 160), level= the error correction - L,
  // M (default), Q or H - and title= the accessible name, else the text itself. color= and
  // background= change the dark and light colours, black on white by default: a reader
  // needs the contrast.

  $padCodeKind = 'qr';

  return include PAD . 'codes/barcode.php';

?>
