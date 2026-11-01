<?php

  // chime - two soft bells a fifth apart, the second a moment after the first: something
  // went well. Sine tones that ring out, a quiet octave above for the shimmer.

  return [
    'title'  => 'Chime',
    'voices' => [
      [ 'wave' => 'sine', 'note' => 'E6', 'length' => 1.0,  'attack' => 0.004, 'decay' => 1.0,  'sustain' => 0, 'gain' => 0.5  ],
      [ 'wave' => 'sine', 'note' => 'B6', 'length' => 1.1,  'attack' => 0.004, 'decay' => 1.1,  'sustain' => 0, 'gain' => 0.4, 'start' => 0.12 ],
      [ 'wave' => 'sine', 'note' => 'E7', 'length' => 0.35, 'attack' => 0.002, 'decay' => 0.35, 'sustain' => 0, 'gain' => 0.08 ],
    ],
  ];

?>
