<?php

  // A single byte between two pieces of text is shifted in with 913, after which a reader goes
  // on in the text submode the text before it ended in. A piece that ends latched in
  // punctuation with an odd number of values is filled with a 29 - in punctuation the latch to
  // alpha (PAL), not the shift it is in the other submodes - so the text after the byte starts
  // in alpha. Each line: the text, then its data codewords (30 x the first value + the second).
  //
  // 1;<>@  1 (alpha) ML ; (mixed: 1) PL then < > @ - 28 1 25 0 1 2 3, padded: 841 750 32 119
  // <<<<<  in alpha now, so each < a punctuation shift: 29 x 30 + 1 = 871 - where the five
  //        values were read as alpha's, "BBBBB"
  // abcde  LL a, b c, d e: 810 32 94 - where read as "}ABCDE"
  // Order  O LL, r d, e r: 447 513 137 - where read as ":rder"
  //
  // The SVG of the last holds the same codewords in its modules (its md5).

  include_once PAD . 'subs/codes/_lib/pdf417.php';

  $size12 = "Size 12();\x01Order";

  $texts = [];

  foreach ( [ "1;<>@\x01<<<<<", "1;<>@\x01abcde", $size12 ] as $text )
    $texts [] = [ 'text' => json_encode ( $text ), 'words' => implode ( ' ', padPdf417Words ( $text ) ) ];

?>
