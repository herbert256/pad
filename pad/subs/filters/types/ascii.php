<?php

  // ascii - the picture as text, a <pre> in place of the <img>: columns= characters across
  // (64), as many lines as the picture's shape asks for, each character one of chars= - the
  // lightest first (' .:-=+*#%@') - by the darkness of the part it stands for. The alt text
  // is the name of the <pre>, role="img": a screen reader says the alt, not the characters;
  // alt='' leaves it decoration.
  //
  //   {img 'photos/harbour.jpg', width=400, filter='ascii', columns=80, alt='The harbour'/}
  //
  // A character of a monospaced face is about 0.6 of its size wide and a line 1 high, so the
  // picture is cut into columns by columns x 0.6 x height / width lines, each the average of
  // what it covers, and the size of the text is the width over the columns over 0.6 - the
  // <pre> as wide as the picture.

  return [

    'form'    => 'markup',
    'ext'     => 'txt',
    'options' => [ 'columns' => [ 'number', 64, 8, 240 ], 'chars' => [ 'text', ' .:-=+*#%@', 64 ] ],

    'make'    => function ( $image, $o ) {

      $w     = imagesx ( $image );
      $h     = imagesy ( $image );
      $cols  = (int) $o ['columns'];
      $rows  = max ( 1, (int) round ( $cols * 0.6 * $h / $w ) );
      $chars = mb_str_split ( $o ['chars'] );
      $count = count ( $chars );
      $lines = [];

      foreach ( padFilterLumas ( padFilterResize ( $image, $cols, $rows ) ) as $row ) {
        $line = '';
        foreach ( $row as $luma )
          $line .= $chars [ min ( $count - 1, (int) floor ( ( 1 - $luma / 255 ) * $count ) ) ];
        $lines [] = rtrim ( $line );
      }

      return implode ( "\n", $lines );

    },

    'show'    => function ( $kept, $o, $w, $h, $alt ) {

      $size = padFilterNum ( $w / $o ['columns'] / 0.6 );

      return '<pre class="pad-img-ascii" style="font-size:' . $size . 'px"'
           . ( $alt !== '' ? ' role="img" aria-label="' . padImgAttr ( $alt ) . '"' : ' aria-hidden="true"' ) . '>'
           . htmlspecialchars ( $kept, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8' ) . '</pre>';

    }

  ];

?>
