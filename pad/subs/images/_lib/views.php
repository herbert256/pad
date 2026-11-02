<?php

  function padImageOptions ( $defaultWidth = 0, $needAlt = TRUE ) {

    global $padCheckSyntax;
    $alt = padTagParm ( 'alt', NULL );
    if ( $needAlt && $alt === NULL && $padCheckSyntax )
      padError ( "the img tag needs an alt= text for whoever cannot see the picture - alt='' when it is decoration" );
    return [ 'alt' => padParmText ( $alt ?? '' ),
      'width' => padTagParm ( 'width', $defaultWidth ), 'height' => padTagParm ( 'height', 0 ),
      'fit' => padTagParm ( 'fit', 'cover' ), 'format' => padTagParm ( 'format', '' ),
      'quality' => padTagParm ( 'quality', 80 ), 'filter' => padTagParm ( 'filter', '' ),
      'focusx' => padTagParm ( 'focusx', 50 ), 'focusy' => padTagParm ( 'focusy', 50 ) ];

  }

  function padImageWidths ( $value ) {

    global $padCheckSyntax;
    $values = is_array ( $value ) ? $value : explode ( ',', is_scalar ( $value ) ? (string) $value : '' );
    $widths = [];
    foreach ( $values as $width ) {
      if ( ! is_scalar ( $width ) || ! preg_match ( '/^\d{1,4}$/D', trim ( (string) $width ) ) || (int) $width < 1 || (int) $width > 4096 ) {
        if ( $padCheckSyntax ) padError ( 'image widths= must list whole pixel widths from 1 to 4096' );
        continue;
      }
      $widths [] = (int) $width;
    }
    $widths = array_values ( array_unique ( $widths ) );
    sort ( $widths );
    if ( count ( $widths ) > 8 ) {
      if ( $padCheckSyntax ) padError ( 'an image picture accepts at most eight responsive widths' );
      $widths = array_slice ( $widths, 0, 8 );
    }
    return $widths ?: [ 320, 640, 960, 1280 ];

  }

  // All variants use the original crop and filter chain. Each is cached independently;
  // neither source pixels nor the per-filter pixel budget can be exceeded by a variant.
  function padImageResponsive ( $path, $type, $turn, $crop, $w, $h, $format, $quality, $base, $filters, $holds, $loops, $pixels, $opts ) {

    $limit = min ( $crop [2], 4096 );
    if ( $loops ) $limit = min ( $limit, floor ( sqrt ( padFilterPixels * $w / $h ) ) );
    $variants = [];
    foreach ( array_unique ( array_merge ( $opts ['widths'], [ $w ] ) ) as $asked ) {
      $width = max ( 1, (int) min ( $asked, $limit ) );
      $height = max ( 1, (int) round ( $width * $h / $w ) );
      $url = padImgThumb ( $path, $type, $turn, $crop, $width, $height, $format, $quality, $base, $filters, $width / $w );
      if ( $url !== NULL ) $variants [$width] = $url;
    }
    if ( ! $variants ) return '';
    ksort ( $variants );
    $set = [];
    foreach ( $variants as $width => $url ) $set [] = "$url {$width}w";
    $srcset = implode ( ', ', $set );
    $src = $variants [$w] ?? end ( $variants );
    $more = ' sizes="' . padImgAttr ( $opts ['sizes'] ) . '"';
    if ( $holds ) $more .= padImgHold ( $src, $holds, $base, $w, $h );
    $img = padImgTag ( $src, $srcset, $w, $h, $opts ['alt'], $pixels ? ' pad-img-pixels' : '', $more );
    // padImgTag's once-per-page style belongs before picture, whose content is sources and img.
    $at = strpos ( $img, '<img ' );
    $style = substr ( $img, 0, $at );
    $img = substr ( $img, $at );
    $sources = '';
    foreach ( array_reverse ( array_keys ( $variants ) ) as $width )
      $sources .= '<source media="(min-width: ' . $width . 'px)" srcset="' . padImgAttr ( $srcset ) . '" sizes="' . padImgAttr ( $opts ['sizes'] ) . '">';
    return ( $filters || $holds ? padFilterStyle () : '' ) . $style . '<picture class="pad-image-picture">' . $sources . $img . '</picture>';

  }

  function padImageFigure ( $image, $caption ) {

    return '<figure class="pad-image-figure">' . $image
      . ( $caption !== '' ? '<figcaption>' . padImgAttr ( $caption ) . '</figcaption>' : '' ) . '</figure>';

  }

  function padImageContactSheet ( $rows, $opts, $columns = 3, $srcField = 'src', $captionField = 'caption' ) {

    global $padApp, $padRoot, $padCheckSyntax;
    if ( ! is_array ( $rows ) || count ( $rows ) > 64 ) {
      if ( $padCheckSyntax ) padError ( 'a contact sheet takes at most 64 image rows' );
      return '';
    }
    if ( ! $rows ) return '';
    $columns = max ( 1, min ( 8, (int) $columns, count ( $rows ) ) );
    $width = max ( 1, min ( 1024, (int) ( $opts ['width'] ?: 200 ) ) );
    $height = max ( 1, min ( 1024, (int) ( $opts ['height'] ?: 150 ) ) );
    $gap = 8;
    $sw = $columns * ( $width + $gap ) + $gap;
    $sh = (int) ceil ( count ( $rows ) / $columns ) * ( $height + $gap ) + $gap;
    if ( $sw * $sh > 16000000 ) {
      if ( $padCheckSyntax ) padError ( 'a contact sheet may contain at most 16 megapixels; reduce width or height' );
      return '';
    }
    $thumbs = []; $captions = [];
    foreach ( $rows as $row ) {
      if ( ! is_array ( $row ) || ! isset ( $row [$srcField] ) || ! is_string ( $row [$srcField] ) ) {
        if ( $padCheckSyntax ) padError ( "each contact sheet row needs a text $srcField field" );
        continue;
      }
      $thumb = padImg ( $row [$srcField], array_replace ( $opts, [ 'width' => $width, 'height' => $height, '_result' => TRUE ] ) );
      if ( ! is_array ( $thumb ) ) continue;
      $thumbs [] = $thumb;
      $caption = $row [$captionField] ?? $row ['alt'] ?? basename ( $row [$srcField] );
      $captions [] = is_scalar ( $caption ) ? (string) $caption : '';
    }
    if ( ! $thumbs ) return '';
    [ $file, $url, $hash ] = padImgName ( [ 'contactsheet-v1', $thumbs, $columns, $width, $height ], $sw, $sh, 'png' );
    if ( ! is_file ( $file ) ) {
      if ( ! padImgDir () ) return '';
      $sheet = imagecreatetruecolor ( $sw, $sh );
      imagefill ( $sheet, 0, 0, imagecolorallocate ( $sheet, 246, 245, 241 ) );
      foreach ( $thumbs as $i => [ $thumb, $w, $h ] ) {
        $path = dirname ( APPS ) . "/www/$padApp/_thumbs/" . basename ( $thumb );
        $image = @imagecreatefromstring ( (string) @file_get_contents ( $path ) );
        if ( ! $image ) continue;
        $x = $gap + ( $i % $columns ) * ( $width + $gap ) + (int) ( ( $width - $w ) / 2 );
        $y = $gap + (int) ( $i / $columns ) * ( $height + $gap ) + (int) ( ( $height - $h ) / 2 );
        imagecopy ( $sheet, $image, $x, $y, 0, 0, $w, $h );
        unset ( $image );
      }
      $temp = dirname ( $file ) . '/.' . $hash . '-' . bin2hex ( random_bytes ( 4 ) );
      if ( ! imagepng ( $sheet, $temp, 6 ) || ! @rename ( $temp, $file ) ) {
        @unlink ( $temp ); padImgFailed ( 'the image contact sheet cannot be written' ); return '';
      }
    }
    $alt = $opts ['alt'] !== '' ? $opts ['alt'] : 'Contact sheet of ' . count ( $thumbs ) . ' images';
    $list = '<ol>' . implode ( '', array_map ( fn ( $text ) => '<li>' . padImgAttr ( $text ) . '</li>', $captions ) ) . '</ol>';
    return '<figure class="pad-image-contactsheet">' . padImgTag ( $url, '', $sw, $sh, $alt ) . '<figcaption>' . $list . '</figcaption></figure>';

  }

?>
