<?php

  // gameover - better luck next time: four notes sinking a semitone each, the last one long
  // and wobbling, like a muted horn - saw waves whose lowpass opens on every note, wah, and
  // closes again on the last.

  return [
    'title'  => 'Game over',
    'level'  => 0.5,
    'voices' => [
      [ 'wave' => 'saw', 'note' => 'D4', 'length' => 0.3, 'attack' => 0.04, 'decay' => 0.1, 'sustain' => 0.8, 'release' => 0.06,
        'gain' => 0.5, 'filter' => [ 'lowpass', 450, 1500, 1.4 ] ],
      [ 'wave' => 'saw', 'note' => 'C#4', 'start' => 0.34, 'length' => 0.3, 'attack' => 0.04, 'decay' => 0.1, 'sustain' => 0.8, 'release' => 0.06,
        'gain' => 0.5, 'filter' => [ 'lowpass', 450, 1500, 1.4 ] ],
      [ 'wave' => 'saw', 'note' => 'C4', 'start' => 0.68, 'length' => 0.3, 'attack' => 0.04, 'decay' => 0.1, 'sustain' => 0.8, 'release' => 0.06,
        'gain' => 0.5, 'filter' => [ 'lowpass', 450, 1500, 1.4 ] ],
      [ 'wave' => 'saw', 'note' => 'B3', 'start' => 1.02, 'length' => 1.0, 'attack' => 0.04, 'decay' => 0.2, 'sustain' => 0.75, 'release' => 0.35,
        'vibrato' => [ 5, 0.35 ], 'gain' => 0.5, 'filter' => [ 'lowpass', 1500, 400, 1.4 ] ],
    ],
  ];

?>
