<?php

  // countdown - ready, set, go: three short beeps half a second apart, then a long one an
  // octave higher. Sine tones, a soft triangle under each for body.

  return [
    'title'  => 'Countdown',
    'level'  => 0.45,
    'voices' => [
      [ 'wave' => 'sine',     'notes' => 'A4 A4 A4', 'step' => 0.5, 'length' => 0.13, 'attack' => 0.004, 'release' => 0.03, 'gain' => 0.6 ],
      [ 'wave' => 'triangle', 'notes' => 'A3 A3 A3', 'step' => 0.5, 'length' => 0.13, 'attack' => 0.004, 'release' => 0.03, 'gain' => 0.15 ],
      [ 'wave' => 'sine',     'note'  => 'A5', 'start' => 1.5, 'length' => 0.6, 'attack' => 0.004, 'decay' => 0.5, 'sustain' => 0.45, 'release' => 0.1, 'gain' => 0.6 ],
      [ 'wave' => 'triangle', 'note'  => 'A4', 'start' => 1.5, 'length' => 0.6, 'attack' => 0.004, 'decay' => 0.5, 'sustain' => 0.45, 'release' => 0.1, 'gain' => 0.15 ],
    ],
  ];

?>
