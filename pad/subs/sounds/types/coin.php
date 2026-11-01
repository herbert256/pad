<?php

  // coin - the coin of an old video game: two square notes, B then a long E, a fourth up.

  return [
    'title'  => 'Coin',
    'level'  => 0.35,
    'voices' => [
      [ 'wave' => 'square', 'note' => 'B5', 'length' => 0.075, 'attack' => 0.002, 'release' => 0.005, 'gain' => 0.4 ],
      [ 'wave' => 'square', 'note' => 'E6', 'start' => 0.075, 'length' => 0.42, 'attack' => 0.002, 'decay' => 0.42, 'sustain' => 0, 'gain' => 0.4 ],
    ],
  ];

?>
