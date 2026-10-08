<?php

  // Ratings - the {rating} tag. A score as a row of stars, or hearts, as inline SVG: the
  // whole ones filled, a part of one filled as far as the score goes, the rest empty - and
  // said in words for a screen reader, "4.5 out of 5".
  //
  //   {rating 4.5, max=5}
  //   {rating $score, icon='heart', size=16}
  //
  // padRating        the SVG of a score
  // padRatingShape   the outline of one icon on a 24 by 24 cell: star or heart
  //
  // A part of an icon is a nested <svg> as wide as the part, which clips what lies outside
  // it - no clipPath, so no id that two ratings on one page could share. The part is
  // measured over the drawn icon, x 2 to 22 of its cell, so 0.5 is exactly half of it. The colours are
  // --pad-rating-star, -heart and -empty, light-dark() on .pad-rating, written once per page;
  // the fill attributes carry the light ones for a reader without the style.

  const PAD_RATING_ICONS = [ 'star', 'heart' ];

  function padRating ( $value, $max = 5, $icon = 'star', $size = 20, $label = '' ) {

    $max   = (int) $max;
    $value = max ( 0, min ( $max, (float) $value ) );
    $shape = padRatingShape ( $icon );
    $size  = max ( 8, (int) $size );
    $cell  = 28;
    $width = $max * $cell - 4;
    $label = ( trim ( (string) $label ) !== '' ) ? trim ( (string) $label ) : padRatingNumber ( $value ) . " out of $max";
    $fill  = ( $icon == 'heart' ) ? '#d93b5a' : '#e09a00';
    $body  = '';

    for ( $i = 0; $i < $max; $i++ ) {

      $x    = $i * $cell;
      $frac = max ( 0, min ( 1, $value - $i ) );
      $part = ( $frac >= 1 ) ? 24 : ( ( $frac > 0 ) ? round ( 2 + $frac * 20, 2 ) : 0 );

      if ( $part >= 24 ) {
        $body .= "<path class=\"rt-$icon\" transform=\"translate($x)\" d=\"$shape\" fill=\"$fill\"/>";
        continue;
      }

      $body .= "<path class=\"rt-empty\" transform=\"translate($x)\" d=\"$shape\" fill=\"#dddcd7\"/>";

      if ( $part > 0 )
        $body .= "<svg x=\"$x\" width=\"$part\" height=\"24\" viewBox=\"0 0 $part 24\">"
               . "<path class=\"rt-$icon\" d=\"$shape\" fill=\"$fill\"/></svg>";

    }

    return padRatingStyle ()
         . '<svg xmlns="http://www.w3.org/2000/svg" class="pad-rating" role="img" aria-label="' . padRatingAttr ( $label ) . '"'
         . ' width="' . round ( $size * $width / 24, 2 ) . "\" height=\"$size\" viewBox=\"0 0 $width 24\">"
         . '<title>' . padRatingAttr ( $label ) . '</title>'
         . $body . '</svg>';

  }

  // The star: five points around the middle of the cell, the inner corners at 42 percent of
  // the outer radius, drawn from the top clockwise. The heart: two arcs meeting in a point.

  function padRatingShape ( $icon ) {

    if ( $icon == 'heart' )
      return 'M12 20.6C6.9 16.7 2.7 13.1 2.7 8.8 2.7 6 4.8 3.9 7.5 3.9c1.9 0 3.5 1 4.5 2.6 1-1.6 2.6-2.6 4.5-2.6 2.7 0 4.8 2.1 4.8 4.9 0 4.3-4.2 7.9-9.3 11.8Z';

    $points = [];

    for ( $k = 0; $k < 10; $k++ ) {
      $r         = ( $k % 2 ) ? 4.45 : 10.6;
      $angle     = deg2rad ( -90 + $k * 36 );
      $points [] = round ( 12 + $r * cos ( $angle ), 2 ) . ' ' . round ( 12.9 + $r * sin ( $angle ), 2 );
    }

    return 'M' . implode ( 'L', $points ) . 'Z';

  }

  function padRatingNumber ( $number ) {

    return rtrim ( rtrim ( number_format ( (float) $number, 2, '.', '' ), '0' ), '.' );

  }

  function padRatingStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;
    $roles   = [ 'star'  => [ '#e09a00', '#f5b82e' ],
                 'heart' => [ '#d93b5a', '#f0627d' ],
                 'empty' => [ '#dddcd7', '#3d3d3a' ] ];
    $light   = $both = $rules = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-rating-$role:$day;";
      $both  .= "--pad-rating-$role:light-dark($day,$night);";
      $rules .= ".pad-rating .rt-$role{fill:var(--pad-rating-$role)}";
    }

    return '<style' . padRatingNonce () . '>'
         . ":where(.pad-rating){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-rating){{$both}}}"
         . '.pad-rating{vertical-align:middle;flex:none}'
         . $rules
         . '.pad-rating path{stroke-linejoin:round}'
         . '</style>';

  }

  function padRatingNonce () {

    global $padCsp;

    return ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) ) ? ' nonce="' . padNonce () . '"' : '';

  }

  function padRatingAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
