<?php

  // What the cases of the filters group read of the pictures {image:thumbnail} wrote: a binary answer as
  // facts that can be checked by hand and its md5 - the same picture, the same bytes.
  //
  //   {image:thumbnail 'assets/photo.jpg', width=80, filter='grayscale', alt='x' | php:filtersThumbs(@)/}
  //
  // filtersThumbs  every picture of _thumbs/ the markup names, once: its size, its format,
  //                how many colours it holds, whether every pixel is grey, and its md5
  // filtersAlpha   what a filter left of the transparency of a picture: of every picture of
  //                _thumbs/ the markup names and of a placeholder PNG in it, the corner pixel
  //                and the range of the red of the pixels that show; and the data-color -
  //                for assets/alpha.png: transparent around an ellipse of #e63c28 and a
  //                square of #285adc at half opacity, #814b86 where they overlap
  // filtersSourceKept  whether the img tag still holds a decoded source picture (the memo
  //                of padImgDraw) - a picture of 12 megapixels is some 50 MB of GD's memory

  function filtersThumbs ( $html ) {

    preg_match_all ( '#/_thumbs/([0-9a-f]{16}\.(?:jpg|png|webp))#', (string) $html, $found );

    $facts = [];

    foreach ( array_unique ( $found [1] ) as $name ) {

      $file    = dirname ( APPS ) . "/www/regression/framework/_thumbs/$name";
      $picture = @imagecreatefromstring ( (string) @file_get_contents ( $file ) );

      if ( ! $picture ) {
        $facts [] = "$name missing";
        continue;
      }

      $colours = [];
      $grey    = TRUE;

      for ( $y = 0; $y < imagesy ( $picture ); $y++ )
        for ( $x = 0; $x < imagesx ( $picture ); $x++ ) {
          $c = imagecolorat ( $picture, $x, $y ) & 0xFFFFFF;
          $colours [$c] = TRUE;
          if ( ( $c >> 16 ) != ( ( $c >> 8 ) & 255 ) or ( $c >> 16 ) != ( $c & 255 ) )
            $grey = FALSE;
        }

      $facts [] = substr ( $name, -3 ) . ' ' . imagesx ( $picture ) . 'x' . imagesy ( $picture ) . ' colours ' . count ( $colours )
                . ( $grey ? ' grey' : '' ) . ' md5 ' . md5_file ( $file );

    }

    return implode ( ' | ', $facts );

  }

  function filtersAlpha ( $html ) {

    $facts = [];

    preg_match_all ( '#/_thumbs/([0-9a-f]{16}\.(?:jpg|png|webp))#', (string) $html, $found );

    foreach ( array_unique ( $found [1] ) as $name )
      $facts [] = substr ( $name, -3 ) . ' ' . filtersAlphaFacts ( @imagecreatefromstring ( (string) @file_get_contents ( dirname ( APPS ) . "/www/regression/framework/_thumbs/$name" ) ) );

    if ( preg_match ( '#data-color="([^"]+)"#', (string) $html, $color ) )
      $facts [] = "data-color {$color [1]}";

    if ( preg_match ( '#base64,([A-Za-z0-9+/=]+)#', (string) $html, $png ) )
      $facts [] = 'placeholder ' . filtersAlphaFacts ( @imagecreatefromstring ( base64_decode ( $png [1] ) ) );

    return implode ( ' | ', $facts );

  }

  function filtersSourceKept () {

    if ( ! function_exists ( 'padImgDraw' ) )
      return 'no img tag';

    $memo = ( new ReflectionFunction ( 'padImgDraw' ) ) -> getStaticVariables () ['memo'] ?? NULL;

    return is_object ( $memo [1] ?? NULL ) ? 'a source kept' : 'no source kept';

  }

  // The size, the corner pixel - transparent, or its opacity out of 127 and its colour - and
  // the red of the pixels at least half opaque, from the least to the most. (A pixel all but
  // transparent holds its colour to a few steps: what shows of it is next to nothing.)

  function filtersAlphaFacts ( $picture ) {

    if ( ! $picture )
      return 'missing';

    $corner = imagecolorat ( $picture, 0, 0 );
    $alpha  = ( $corner >> 24 ) & 127;
    $least  = 255;
    $most   = 0;

    for ( $y = 0; $y < imagesy ( $picture ); $y++ )
      for ( $x = 0; $x < imagesx ( $picture ); $x++ ) {
        $c = imagecolorat ( $picture, $x, $y );
        if ( ( ( $c >> 24 ) & 127 ) > 63 )
          continue;
        $least = min ( $least, ( $c >> 16 ) & 255 );
        $most  = max ( $most,  ( $c >> 16 ) & 255 );
      }

    return imagesx ( $picture ) . 'x' . imagesy ( $picture ) . ' corner '
         . ( $alpha == 127 ? 'transparent' : ( $alpha ? 'opacity ' . ( 127 - $alpha ) . ' ' : 'opaque ' ) . sprintf ( '#%06x', $corner & 0xFFFFFF ) )
         . " red $least to $most";

  }

?>
