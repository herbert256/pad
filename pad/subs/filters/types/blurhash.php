<?php

  // blurhash - a placeholder: Wolt's BlurHash of the picture in data-blurhash, and the hash
  // decoded again as a small PNG behind the <img> - the blur of its colours the page shows
  // until the picture has loaded. components= the cosine terms across and down ('4,3').
  //
  //   {img 'photos/harbour.jpg', width=400, filter='blurhash', alt='The harbour'/}
  //
  // The hash is of the 1x picture, the filters before it applied, made at most 64 pixels on its
  // longer side first - each pixel the average of what it covers, as a picture's hash is
  // usually made; the decoded PNG is 32 pixels on its longer side, drawn over the whole box
  // (background-size: cover). The encoder and decoder are the reference algorithm
  // (_lib/blurhash.php).

  return [

    'form'       => 'placeholder',
    'background' => TRUE,
    'options'    => [ 'components' => [ 'pair', [ 4, 3 ], 1, 9 ] ],

    'hold'       => function ( $image, $o ) {

      include_once PAD . 'subs/filters/_lib/blurhash.php';

      $w     = imagesx ( $image );
      $h     = imagesy ( $image );
      $small = padFilterResize ( $image, (int) round ( $w * min ( 1, 64 / max ( $w, $h ) ) ), (int) round ( $h * min ( 1, 64 / max ( $w, $h ) ) ) );
      $hash  = padBlurhashEncode ( padBlurhashPixels ( $small ), $o ['components'] [0], $o ['components'] [1] );

      $dw     = max ( 1, (int) round ( $w * 32 / max ( $w, $h ) ) );
      $dh     = max ( 1, (int) round ( $h * 32 / max ( $w, $h ) ) );
      $picked = imagecreatetruecolor ( $dw, $dh );

      foreach ( padBlurhashDecode ( $hash, $dw, $dh ) as $y => $row )
        foreach ( $row as $x => list ( $r, $g, $b ) )
          imagesetpixel ( $picked, $x, $y, ( $r << 16 ) | ( $g << 8 ) | $b );

      ob_start ();
      imagepng ( $picked, NULL, 9 );
      $png = ob_get_clean ();

      return [ 'style' => [ 'background-image' => 'url(data:image/png;base64,' . base64_encode ( $png ) . ')', 'background-size' => 'cover' ],
               'attrs' => [ 'data-blurhash' => $hash ] ];

    }

  ];

?>
