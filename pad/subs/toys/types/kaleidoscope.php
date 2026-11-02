<?php

  // kaleidoscope - draw in one wedge and see it mirrored round the circle, as in the tube of
  // mirrors: every stroke drawn again in each wedge, flipped in every other, the colour
  // turning as you go. When nobody draws, a pen of its own wanders and draws. Save keeps the
  // picture as an image. mirrors= how many wedges (8 unless given, 3 to 24); brush= the width
  // of the line (3, 1 to 20); colour= rainbow, ink or palette - the page's own colours.
  //
  //   {toy:kaleidoscope mirrors=12, colour='palette'/}

  return padToy ( 'kaleidoscope', 'Kaleidoscope', [
    'mirrors' => padToyInt ( 'mirrors', 8, 3, 24 ),
    'brush'   => padToyInt ( 'brush', 3, 1, 20 ),
    'colour'  => padToyChoice ( 'colour', 'rainbow', [ 'rainbow', 'ink', 'palette' ] )
  ] );

?>
