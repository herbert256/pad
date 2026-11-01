<?php

  // tick - the tick of a clock: a high wooden tap, shorter and brighter than a click.

  return [
    'title'  => 'Tick',
    'voices' => [
      [ 'wave' => 'triangle', 'note' => 2400, 'length' => 0.02, 'attack' => 0.0005, 'decay' => 0.019, 'sustain' => 0, 'release' => 0.001, 'gain' => 0.6 ],
      [ 'wave' => 'noise', 'note' => 5000, 'length' => 0.006, 'attack' => 0.0003, 'decay' => 0.0055, 'sustain' => 0, 'release' => 0.0002,
        'gain' => 0.3, 'filter' => [ 'highpass', 3000, 3000, 0.7 ] ],
    ],
  ];

?>
