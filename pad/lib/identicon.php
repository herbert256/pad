<?php

  // Identicons - the {identicon} tag. A value - an e-mail address, a user id, a key - becomes
  // a symmetric pattern of 5 by 5 cells with a colour of its own, as inline SVG: a picture
  // that tells accounts apart at a glance without anyone uploading one, and without sending
  // the address anywhere.
  //
  //   {identicon $email}
  //   {identicon $user.id, size=32, title='Your identicon'}
  //
  // padIdenticon        the SVG of a value
  // padIdenticonCells   the pattern: 5 rows of 5 booleans, the left half mirrored right
  // padIdenticonColour  the colour: a hue from the hash, at a saturation and lightness that
  //                     read on the light square behind the pattern
  //
  // The value is trimmed and lower-cased - an e-mail address in any spelling is the same
  // picture - and hashed with SHA-256: 15 nibbles give the cells of the three left columns,
  // the middle one included, and the last bytes the colour. The light square behind it is
  // --pad-identicon-background, light-dark() on .pad-identicon, written once per page - a
  // little dimmer in a dark scheme, but light in both, so every colour keeps its contrast.

  function padIdenticon ( $value, $size = 64, $title = '' ) {

    $hash   = hash ( 'sha256', mb_strtolower ( trim ( (string) $value ) ) );
    $cells  = padIdenticonCells ( $hash );
    $colour = padIdenticonColour ( $hash );
    $size   = max ( 8, (int) $size );
    $title  = ( trim ( (string) $title ) !== '' ) ? trim ( (string) $title ) : 'Identicon';
    $path   = '';

    // A cell is 1 unit on a 6 by 6 view, which leaves half a cell of margin around the
    // pattern; the dark cells of a row are joined into runs, as the qr code does.

    foreach ( $cells as $y => $row )
      for ( $x = 0; $x < 5; $x++ )
        if ( $row [$x] ) {
          $run = 1;
          while ( $x + $run < 5 and $row [$x + $run] )
            $run++;
          $path .= 'M' . ( $x + 0.5 ) . ',' . ( $y + 0.5 ) . "h{$run}v1h-{$run}z";
          $x += $run;
        }

    return padIdenticonStyle ()
         . '<svg xmlns="http://www.w3.org/2000/svg" class="pad-identicon" role="img" aria-label="' . padIdenticonAttr ( $title ) . '"'
         . " width=\"$size\" height=\"$size\" viewBox=\"0 0 6 6\" shape-rendering=\"crispEdges\">"
         . '<title>' . padIdenticonAttr ( $title ) . '</title>'
         . '<rect class="pi-back" width="6" height="6" fill="#f0f0ee"/>'
         . "<path fill=\"$colour\" d=\"$path\"/></svg>";

  }

  function padIdenticonCells ( $hash ) {

    $cells = [];

    for ( $y = 0; $y < 5; $y++ )
      for ( $x = 0; $x < 3; $x++ ) {
        $on = hexdec ( $hash [ $x * 5 + $y ] ) % 2 == 0;
        $cells [$y] [$x]     = $on;
        $cells [$y] [4 - $x] = $on;
      }

    foreach ( $cells as $y => $row )
      ksort ( $cells [$y] );

    return $cells;

  }

  // The hue from the last three bytes, written as an RGB hex colour: the attribute reads in
  // every SVG renderer, an e-mail program's included.

  function padIdenticonColour ( $hash ) {

    $hue = hexdec ( substr ( $hash, -6 ) ) % 360;
    $sat = 0.55 + ( hexdec ( substr ( $hash, -8, 2 ) ) % 20 ) / 100;
    $lum = 0.38 + ( hexdec ( substr ( $hash, -10, 2 ) ) % 10 ) / 100;

    $c = ( 1 - abs ( 2 * $lum - 1 ) ) * $sat;
    $h = $hue / 60;
    $x = $c * ( 1 - abs ( fmod ( $h, 2 ) - 1 ) );
    $m = $lum - $c / 2;

    list ( $r, $g, $b ) = [ [ $c, $x, 0 ], [ $x, $c, 0 ], [ 0, $c, $x ], [ 0, $x, $c ], [ $x, 0, $c ], [ $c, 0, $x ] ] [ (int) $h % 6 ];

    return sprintf ( '#%02x%02x%02x', round ( ( $r + $m ) * 255 ), round ( ( $g + $m ) * 255 ), round ( ( $b + $m ) * 255 ) );

  }

  // The background, once per page.

  function padIdenticonStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;

    return '<style' . padIdenticonNonce () . '>'
         . ':where(.pad-identicon){--pad-identicon-background:#f0f0ee}'
         . '@supports (color:light-dark(#000,#fff)){:where(.pad-identicon){--pad-identicon-background:light-dark(#f0f0ee,#dddcd6)}}'
         . '.pad-identicon{vertical-align:middle;flex:none}'
         . '.pad-identicon .pi-back{fill:var(--pad-identicon-background)}'
         . '</style>';

  }

  function padIdenticonNonce () {

    global $padCsp;

    return ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) ) ? ' nonce="' . padNonce () . '"' : '';

  }

  function padIdenticonAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
