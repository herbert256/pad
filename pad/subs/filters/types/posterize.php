<?php

  // posterize - every colour channel cut to levels= steps (4), as a screen print of flat
  // areas; written as a PNG unless format= says otherwise.
  //
  //   {pad:img 'photos/harbour.jpg', width=400, filter='posterize', levels=3, alt='The harbour'/}

  return [

    'form'     => 'image',
    'loops'    => TRUE,
    'lossless' => TRUE,
    'options'  => [ 'levels' => [ 'number', 4, 2, 32, 'whole' ] ],

    'apply'    => function ( $image, $o, $scale ) {

      $n    = (int) $o ['levels'] - 1;
      $step = [];

      for ( $v = 0; $v < 256; $v++ )
        $step [$v] = (int) round ( round ( $v * $n / 255 ) * 255 / $n );

      return padFilterMap ( $image, fn ( $r, $g, $b ) => ( $step [$r] << 16 ) | ( $step [$g] << 8 ) | $step [$b] );

    }

  ];

?>
