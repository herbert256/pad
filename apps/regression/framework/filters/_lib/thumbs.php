<?php

  // What the cases of the filters group read of the pictures {img} wrote: a binary answer as
  // facts that can be checked by hand and its md5 - the same picture, the same bytes.
  //
  //   {img 'assets/photo.jpg', width=80, filter='grayscale', alt='x' | php:filtersThumbs(@)/}
  //
  // filtersThumbs  every picture of _thumbs/ the markup names, once: its size, its format,
  //                how many colours it holds, whether every pixel is grey, and its md5

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

?>
