<?php

  // click - the short dry click of a switch or a key, a burst of noise and a tap of tone.

  return [
    'title'  => 'Click',
    'voices' => [
      [ 'wave' => 'noise', 'length' => 0.012, 'attack' => 0.0005, 'decay' => 0.011, 'sustain' => 0, 'release' => 0.001,
        'gain' => 0.6, 'filter' => [ 'bandpass', 3200, 3200, 1.5 ] ],
      [ 'wave' => 'sine', 'note' => 1500, 'length' => 0.015, 'attack' => 0.0005, 'decay' => 0.014, 'sustain' => 0, 'release' => 0.001, 'gain' => 0.35 ],
    ],
  ];

?>
