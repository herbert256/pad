<?php

  // orbits - planets and stars pulling at each other by gravity, every body on every other,
  // drawing their paths as they go: a sun with its planets, two stars round each other, three
  // bodies chasing along one figure eight, a swarm that falls together. Drag across the sky
  // to throw in a planet - the drag is its speed - and bodies that meet merge into one.
  // system= what it starts with - solar (unless given), binary, eight or swarm; trail= the
  // points of each path (300, 0 to 2000); speed= how fast time runs (5, 1 to 10).
  //
  //   {orbits system='eight', trail=1000/}

  return padToy ( 'orbits', 'Orbits', [
    'system' => padToyChoice ( 'system', 'solar', [ 'solar', 'binary', 'eight', 'swarm' ] ),
    'trail'  => padToyInt ( 'trail', 300, 0, 2000 ),
    'speed'  => padToyInt ( 'speed', 5, 1, 10 )
  ] );

?>
