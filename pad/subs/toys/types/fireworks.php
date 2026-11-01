<?php

  // fireworks - shells climb into a night sky and burst into sparks that fall and fade: a
  // round peony, a ring, a drooping willow, a palm and a crackling crossette. Click the sky to
  // send one up to that spot; the show goes on by itself as well. rate= how many shells
  // the show sends up by itself, from 0 - only the ones clicked - to 10 (4 unless given);
  // gravity= how fast the sparks fall (5, 0 to 10); shell= one kind of shell for the whole
  // show, else a mix of all.
  //
  //   {fireworks rate=8, shell='willow'/}

  return padToy ( 'fireworks', 'Fireworks', [
    'rate'    => padToyInt ( 'rate', 4, 0, 10 ),
    'gravity' => padToyInt ( 'gravity', 5, 0, 10 ),
    'shell'   => padToyChoice ( 'shell', 'mix', [ 'mix', 'peony', 'ring', 'willow', 'palm', 'crossette' ] )
  ] );

?>
