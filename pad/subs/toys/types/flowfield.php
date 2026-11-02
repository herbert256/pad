<?php

  // flowfield - thousands of particles drift on a field of currents made from noise, each
  // leaving a fine trail, and slowly paint the shape of the flow - silk, smoke, marbled
  // paper. The pointer stirs a whirl into the current, a click calls up a new field, and the
  // field itself drifts as time goes by. particles= how many (1500 unless given, 100 to
  // 6000); scale= how wide the currents are (4, 1 to 10); speed= how fast the particles go
  // (3, 1 to 10); fade= how soon the trails fade (3, 0 - never - to 10).
  //
  //   {toy:flowfield particles=3000, scale=6, fade=0/}

  return padToy ( 'flowfield', 'Flow field', [
    'particles' => padToyInt ( 'particles', 1500, 100, 6000 ),
    'scale'     => padToyInt ( 'scale', 4, 1, 10 ),
    'speed'     => padToyInt ( 'speed', 3, 1, 10 ),
    'fade'      => padToyInt ( 'fade', 3, 0, 10 )
  ] );

?>
