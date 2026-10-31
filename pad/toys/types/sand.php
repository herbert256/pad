<?php

  // sand - a falling sand box: paint with sand that piles up, water that runs and finds its
  // level, walls, wood that burns, fire that rises and spreads, and plants that grow wherever
  // water touches them - each grain moved by a few rules of its own, every frame. Pick a
  // material and paint with the pointer, the number keys pick one too. width= and height=
  // the grains across and down (160 by 100 unless given, 60 to 300 and 40 to 200);
  // material= the one to paint with first, sand unless given; brush= its size (3, 1 to 12);
  // scene= what the box starts with - hourglass, garden or empty.
  //
  //   {sand scene='garden', material='fire'/}

  return padToy ( 'sand', 'Falling sand', [
    'width'    => padToyInt ( 'width', 160, 60, 300 ),
    'height'   => padToyInt ( 'height', 100, 40, 200 ),
    'material' => padToyChoice ( 'material', 'sand', [ 'sand', 'water', 'wall', 'wood', 'fire', 'plant', 'erase' ] ),
    'brush'    => padToyInt ( 'brush', 3, 1, 12 ),
    'scene'    => padToyChoice ( 'scene', 'hourglass', [ 'hourglass', 'garden', 'empty' ] )
  ] );

?>
