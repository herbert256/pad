<?php

  // boids - a flock of birds from three rules each bird follows by itself: keep apart from
  // the birds close by, fly the way the ones around fly, and steer to their middle - Craig
  // Reynolds' boids. The sliders weigh the three rules; the pointer is a hawk the birds flee
  // from, and a click scatters them. birds= how many (150 unless given, 10 to 600);
  // separation=, alignment= and cohesion= the weights of the rules (5, 0 to 10); speed= how
  // fast they fly (5, 1 to 10).
  //
  //   {toy:boids birds=300, cohesion=8/}

  return padToy ( 'boids', 'Boids', [
    'birds'      => padToyInt ( 'birds', 150, 10, 600 ),
    'separation' => padToyInt ( 'separation', 5, 0, 10 ),
    'alignment'  => padToyInt ( 'alignment', 5, 0, 10 ),
    'cohesion'   => padToyInt ( 'cohesion', 5, 0, 10 ),
    'speed'      => padToyInt ( 'speed', 5, 1, 10 )
  ] );

?>
