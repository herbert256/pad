<?php

  // pharmacode - One-track Pharmacode for the packaging artwork preview example. Accepts 3..131070; leading zeroes do not change the encoded integer. No checksum, start/stop bars, supplementary colour bars or automatic reversal. The rightmost bar has weight one; narrow bars contribute one times that weight, wide bars two, doubling towards the left.
  //
  // The default SVG uses explicit millimetres: narrow 0.5 mm, wide 1.5 mm, gaps 1 mm, height 8 mm (`height` 8..100 mm), with 6 mm clear space at both ends. Preserve printed physical dimensions and orientation. Uniform-width codes are accepted mathematically but need special care in real readers. This preview is not a claim of production scanning compliance or a verified packaging-line integration.
  //
  // Authoritative specification: [Laetus Pharmacode Guide, fourth edition July 1997](https://www.gomaro.ch/ftproot/Laetus_PHARMA-CODE.pdf), sections 1.2, 2.2 and 4.3–4.4. Original PAD implementation; independently generated fixtures use Zint 2.16.0.9 (`zint -b PHARMA -d NUMBER --dump`); tests additionally decode bars with the forward recurrence. Zint is not needed at runtime. No guide artwork or source code is copied.
  //
  // `{barcode:pharmacode "117480", title="Packaging artwork preview code 117480"/}`. PHP: `padPharmacodeSvg("117480")` after the standard barcode/QR helpers. No external requests are made.

include_once PAD.'subs/codes/_lib/pharmacode.php';
try{return padPharmacodeSvg(padCodeText(FALSE),padTagParm('height',8),padParmText(padTagParm('title','')));}
catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
