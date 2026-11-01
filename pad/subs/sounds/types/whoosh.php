<?php

  // whoosh - air rushing past: noise through a bandpass filter that sweeps up from low to
  // high, swelling and fading.

  return [
    'title'  => 'Whoosh',
    'voices' => [
      [ 'wave' => 'noise', 'length' => 0.5, 'attack' => 0.2, 'decay' => 0.3, 'sustain' => 0, 'release' => 0.02,
        'gain' => 0.9, 'filter' => [ 'bandpass', 300, 4200, 1.4 ] ],
    ],
  ];

?>
