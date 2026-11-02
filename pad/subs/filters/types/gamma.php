<?php
  // gamma - adjust the midtones in encoded sRGB channel space. Gamma above 1 lightens;
  // alpha is untouched.
  //
  //   {pad:img 'photos/harbour.jpg', width=400, filter='gamma', gamma=1.8, alt='The harbour with lighter midtones'/}
  //
  return [ 'form' => 'image', 'loops' => TRUE,
    'options' => [ 'gamma' => [ 'number', 1, 0.01, 100 ] ],
    'apply' => function ( $image, $options, $scale ) {
      $gamma = $options ['gamma'];
      if ( ! is_numeric ( $gamma ) or ! is_finite ( (float) $gamma ) or $gamma < .01 or $gamma > 100 ) {
        padError ( 'gamma must be finite and between 0.01 and 100' ); return $image;
      }
      $table = [];
      for ( $i = 0; $i < 256; $i++ ) $table [$i] = (int) round ( 255 * ( $i / 255 ) ** ( 1 / $gamma ) );
      return padFilterMap ( $image, fn ( $r, $g, $b ) => ( $table [$r] << 16 ) | ( $table [$g] << 8 ) | $table [$b] );
    }
  ];
?>
