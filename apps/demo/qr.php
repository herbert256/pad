<?php

  $title = 'QR Codes';

  // The text to encode: what the form posted - its rules, on the field in qr.pad, are kept
  // before this runs - else the address of PAD itself.

  if ( ! padPosted ( 'qr' ) or ! is_string ( $text ?? NULL ) )
    $text = 'https://github.com/herbert256/pad';

  $levels = [
    [ 'level' => 'L', 'restores' => '7%'  ],
    [ 'level' => 'M', 'restores' => '15%' ],
    [ 'level' => 'Q', 'restores' => '25%' ],
    [ 'level' => 'H', 'restores' => '30%' ],
  ];

  $wifi = 'WIFI:T:WPA;S:PAD Demo;P:inversion-of-control;;';

?>
