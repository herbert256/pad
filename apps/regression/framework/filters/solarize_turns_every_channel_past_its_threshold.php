<?php

  // The solarize filter channel by channel, worked out by hand: a channel at the cut,
  // threshold x 255 / 100, or past it becomes 255 less itself, one below it stays. At 50
  // (127.5) the greys 0 64 127 128 192 255 become 0 64 127 127 63 0 - 128 is the first to turn,
  // as with PIL's ImageOps.solarize at its default 128 and ImageMagick's -solarize 50%. Each
  // channel on its own: #20c080 is #203f7f. At 0 everything turns, as invert; at 100 only 255.
  // A pixel keeps its opacity. And the photo as {image:thumbnail} makes it, the same bytes as PIL's
  // solarize gave for the same picture.

  include_once PAD . 'subs/images/filters/_lib/filter.php';

  $spec = include PAD . 'subs/images/filters/types/solarize.php';

  $pixels = function ( $colours, $threshold, $alpha = 0 ) use ( $spec ) {
    $image = imagecreatetruecolor ( count ( $colours ), 1 );
    imagealphablending ( $image, FALSE );
    foreach ( $colours as $x => $c )
      imagesetpixel ( $image, $x, 0, ( $alpha << 24 ) | $c );
    $image = $spec ['apply'] ( $image, [ 'threshold' => $threshold ], 1 );
    $out   = [];
    for ( $x = 0; $x < imagesx ( $image ); $x++ ) {
      $c     = imagecolorat ( $image, $x, 0 );
      $out[] = sprintf ( '#%06x', $c & 0xFFFFFF ) . ( ( $c >> 24 ) & 127 ? ' alpha ' . ( ( $c >> 24 ) & 127 ) : '' );
    }
    return implode ( ' ', $out );
  };

  $greys = [ 0x000000, 0x404040, 0x7f7f7f, 0x808080, 0xc0c0c0, 0xffffff ];

  $checks = [
    [ 'check' => 'greys at 50',        'got' => $pixels ( $greys, 50 ) ],
    [ 'check' => 'a colour at 50',     'got' => $pixels ( [ 0x20c080 ], 50 ) ],
    [ 'check' => 'greys at 0',         'got' => $pixels ( $greys, 0 ) ],
    [ 'check' => 'greys at 100',       'got' => $pixels ( $greys, 100 ) ],
    [ 'check' => 'half transparent',   'got' => $pixels ( [ 0xc0c0c0 ], 50, 64 ) ],
  ];

?>
