<?php

  // thumbhash - a placeholder: Evan Wallace's ThumbHash of the picture in data-thumbhash, and
  // the hash decoded again as a small PNG behind the <img> - more of the picture than a
  // BlurHash in fewer bytes, its proportion and its transparency kept.
  //
  //   {pad:img 'photos/harbour.jpg', width=400, filter='thumbhash', alt='The harbour'/}
  //
  // The hash - some 25 bytes, in base64 in data-thumbhash for a page's own script - is of the
  // 1x picture as shown, every image filter of the list applied wherever the placeholder
  // stands in it, made at most 100 pixels on its longer side first, each pixel the average of
  // what it covers, as the reference asks; the decoded PNG is at most 32 pixels on its longer
  // side, drawn over the whole box (background-size: cover). The encoder and decoder are the
  // reference implementation (_lib/thumbhash.php, MIT). Unlike blurhash and lqip it keeps
  // transparency: the hash holds the opacity, and a logo on a transparent ground gets a
  // placeholder that is transparent where the logo is - the --pad-img-placeholder colour
  // shows there, as it does behind any picture.

  return [

    'form'       => 'placeholder',
    'background' => TRUE,

    'hold'       => function ( $image, $o ) {

      include_once PAD . 'subs/filters/_lib/thumbhash.php';

      $w     = imagesx ( $image );
      $h     = imagesy ( $image );
      $f     = min ( 1, 100 / max ( $w, $h ) );
      $small = ( $f < 1 ) ? padFilterResize ( $image, max ( 1, (int) round ( $w * $f ) ), max ( 1, (int) round ( $h * $f ) ) ) : $image;
      $hash  = padThumbhashEncode ( imagesx ( $small ), imagesy ( $small ), padThumbhashPixels ( $small ) );

      list ( $dw, $dh, $rgba ) = padThumbhashDecode ( $hash );

      if ( ! $dw )
        return [];

      $alpha  = (bool) ( ord ( $hash [2] ) & 0x80 );
      $picked = imagecreatetruecolor ( $dw, $dh );

      if ( $alpha ) {
        imagealphablending ( $picked, FALSE );
        imagesavealpha ( $picked, TRUE );
      }

      for ( $y = 0, $i = 0; $y < $dh; $y++ )
        for ( $x = 0; $x < $dw; $x++, $i += 4 )
          imagesetpixel ( $picked, $x, $y, ( $alpha ? ( 127 - ( $rgba [$i + 3] >> 1 ) ) << 24 : 0 )
                                         | ( $rgba [$i] << 16 ) | ( $rgba [$i + 1] << 8 ) | $rgba [$i + 2] );

      ob_start ();
      imagepng ( $picked, NULL, 9 );
      $png = ob_get_clean ();

      return [ 'style' => [ 'background-image' => 'url(data:image/png;base64,' . base64_encode ( $png ) . ')', 'background-size' => 'cover' ],
               'attrs' => [ 'data-thumbhash' => base64_encode ( $hash ) ] ];

    }

  ];

?>
