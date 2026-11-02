<?php
  // emboss - reveal edges as a gray relief with a directional luminance kernel. Strength
  // controls the relief; alpha is preserved and the sampling follows pixel density.
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='emboss', strength=1.5, alt='The harbour in relief'/}
  //
  return [ 'form' => 'image', 'loops' => TRUE, 'lossless' => TRUE,
    'options' => [ 'strength' => [ 'number', 1, 0, 10 ] ],
    'apply' => function ( $image, $options, $scale ) {
      $strength = $options ['strength']; $w = imagesx ( $image ); $h = imagesy ( $image );
      $out = imagecreatetruecolor ( $w, $h ); imagealphablending ( $out, FALSE ); imagesavealpha ( $out, TRUE );
      $stride = max ( 1, (int) floor ( $scale ) ); $part = min ( 1, max ( 0, $scale - $stride ) );
      $kernel = [ [-2,-1,0],[-1,0,1],[0,1,2] ];
      for ( $y = 0; $y < $h; $y++ )
        for ( $x = 0; $x < $w; $x++ ) {
          $center = imagecolorat ( $image, $x, $y );
          $luma = padFilterLuma ( ( $center >> 16 ) & 255, ( $center >> 8 ) & 255, $center & 255 );
          $sum = 0;
          for ( $step = 0; $step <= ( $part ? 1 : 0 ); $step++ ) {
            $delta = $stride + $step; $weight = $part ? ( $step ? $part : 1 - $part ) : 1;
            for ( $dy = -1; $dy <= 1; $dy++ )
              for ( $dx = -1; $dx <= 1; $dx++ ) {
                $pixel = imagecolorat ( $image, max ( 0, min ( $w - 1, $x + $dx * $delta ) ), max ( 0, min ( $h - 1, $y + $dy * $delta ) ) );
                $opacity = 1 - ( ( $pixel >> 24 ) & 127 ) / 127;
                $neighbor = padFilterLuma ( ( $pixel >> 16 ) & 255, ( $pixel >> 8 ) & 255, $pixel & 255 );
                $sum += $weight * $kernel [$dy + 1][$dx + 1] * ( $opacity * $neighbor + ( 1 - $opacity ) * $luma );
              }
          }
          $gray = max ( 0, min ( 255, (int) round ( 128 + $strength * $sum / 4 ) ) );
          imagesetpixel ( $out, $x, $y, ( $center & 0x7F000000 ) | ( $gray << 16 ) | ( $gray << 8 ) | $gray );
        }
      return $out;
    }
  ];
?>
