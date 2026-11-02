<?php

  // duotone - the picture in two colours: its luminance laid on the line from the first of
  // colors= in the shadows to the second in the highlights ('#16213e, #f5c26b').
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='duotone', colors='#123, #fc6', alt='The harbour'/}

  return [

    'form'    => 'image',
    'loops'   => TRUE,
    'options' => [ 'colors' => [ 'colors', [ '#16213e', '#f5c26b' ] ] ],

    'apply'   => function ( $image, $o, $scale ) {

      $dark  = padFilterRgb ( $o ['colors'] [0] );
      $light = padFilterRgb ( $o ['colors'] [1] );
      $ramp  = [];

      for ( $l = 0; $l < 256; $l++ )
        $ramp [$l] = padFilterMix ( $dark, $light, $l / 255 );

      return padFilterMap ( $image, fn ( $r, $g, $b ) => $ramp [ padFilterLuma ( $r, $g, $b ) ] );

    }

  ];

?>
