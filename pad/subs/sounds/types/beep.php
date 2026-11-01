<?php

  // beep - the plain beep of a device: one steady tone, A5, for a sixth of a second - a
  // timer done, a scan taken. A sine, a quiet octave above it for a little edge.

  return [
    'title'  => 'Beep',
    'level'  => 0.4,
    'voices' => [
      [ 'wave' => 'sine', 'note' => 'A5', 'length' => 0.16, 'attack' => 0.004, 'release' => 0.02, 'gain' => 0.8 ],
      [ 'wave' => 'sine', 'note' => 'A6', 'length' => 0.16, 'attack' => 0.004, 'release' => 0.02, 'gain' => 0.08 ],
    ],
  ];

?>
