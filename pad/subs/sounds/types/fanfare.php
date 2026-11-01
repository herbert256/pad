<?php

  // fanfare - you won: three quick notes on G, then a held C major chord, like brass - saw
  // waves behind a lowpass filter that opens as each note swells, a slow vibrato on the chord.

  return [
    'title'  => 'Fanfare',
    'level'  => 0.5,
    'voices' => [
      [ 'wave' => 'saw', 'notes' => 'G4 G4 G4', 'step' => 0.12, 'length' => 0.1, 'attack' => 0.012, 'decay' => 0.05, 'sustain' => 0.7, 'release' => 0.03,
        'gain' => 0.45, 'filter' => [ 'lowpass', 1600, 2400, 0.9 ] ],
      [ 'wave' => 'saw', 'note' => 'C5', 'start' => 0.36, 'length' => 0.85, 'attack' => 0.03, 'decay' => 0.2, 'sustain' => 0.7, 'release' => 0.2,
        'vibrato' => [ 5.5, 0.12 ], 'gain' => 0.4, 'filter' => [ 'lowpass', 700, 2800, 0.9 ] ],
      [ 'wave' => 'saw', 'note' => 'E5', 'start' => 0.36, 'length' => 0.85, 'attack' => 0.03, 'decay' => 0.2, 'sustain' => 0.7, 'release' => 0.2,
        'vibrato' => [ 5.5, 0.12 ], 'gain' => 0.3, 'filter' => [ 'lowpass', 700, 2800, 0.9 ] ],
      [ 'wave' => 'saw', 'note' => 'G5', 'start' => 0.36, 'length' => 0.85, 'attack' => 0.03, 'decay' => 0.2, 'sustain' => 0.7, 'release' => 0.2,
        'vibrato' => [ 5.5, 0.12 ], 'gain' => 0.3, 'filter' => [ 'lowpass', 700, 2800, 0.9 ] ],
      [ 'wave' => 'triangle', 'note' => 'C4', 'start' => 0.36, 'length' => 0.85, 'attack' => 0.03, 'decay' => 0.2, 'sustain' => 0.7, 'release' => 0.2, 'gain' => 0.25 ],
    ],
  ];

?>
