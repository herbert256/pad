<?php

  // The default level of a pdf417 code is the standard's recommendation for the data's size,
  // which ends at 863 data codewords - level 5's 64 check words leave room for 864. Beyond it
  // the default is the largest level that still holds the text, where such a text was refused
  // as too long though level=4 or lower held it. Each line: the text, its data codewords (with
  // the length descriptor), the level the default symbol has - the level= whose symbol is the
  // same - and its rows. 1700 letters keep level 5; 2550 digits (872 codewords) take level 4,
  // 2640 (902) level 3, 2680 (916) level 2.

  include_once PAD . 'subs/codes/_lib/pdf417.php';

  mt_srand ( 7 );

  $letters = '';

  for ( $i = 0; $i < 1700; $i++ )
    $letters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ ' [ mt_rand ( 0, 26 ) ];

  $symbols = [];

  foreach ( [ '1700 letters' => $letters, '2550 digits' => str_repeat ( '0123456789', 255 ),
              '2640 digits' => str_repeat ( '0123456789', 264 ), '2680 digits' => str_repeat ( '0123456789', 268 ) ] as $name => $text ) {
    $matrix = padPdf417Matrix ( $text );
    $level  = 'none';
    for ( $l = 0; $l <= 8; $l++ )
      if ( $matrix and padPdf417Matrix ( $text, $l ) === $matrix )
        $level = $l;
    $symbols [] = [ 'symbol' => "$name, " . ( count ( padPdf417Words ( $text ) ) + 1 ) . ' codewords: '
                              . ( $matrix ? "level $level, " . count ( $matrix ) . ' rows' : 'refused' ) ];
  }

?>
