<?php

  // error - two low notes falling a tritone, buzzing and dull: that did not work. A saw wave
  // behind a lowpass filter.

  return [
    'title'  => 'Error',
    'voices' => [
      [ 'wave' => 'saw', 'notes' => 'B3 F3', 'step' => 0.16, 'length' => 0.2, 'attack' => 0.005, 'decay' => 0.15, 'sustain' => 0.4, 'release' => 0.05,
        'gain' => 0.5, 'filter' => [ 'lowpass', 1400, 1400, 1.2 ] ],
      [ 'wave' => 'square', 'notes' => 'B2 F2', 'step' => 0.16, 'length' => 0.2, 'attack' => 0.005, 'decay' => 0.15, 'sustain' => 0.4, 'release' => 0.05,
        'gain' => 0.25, 'filter' => [ 'lowpass', 900, 900, 0.8 ] ],
    ],
  ];

?>
