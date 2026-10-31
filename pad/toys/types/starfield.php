<?php

  // starfield - flying through space: stars come out of the dark ahead and rush past, faster
  // the nearer they get. The pointer steers where the ship is heading, holding the button down
  // speeds it up, and warp stretches every star into a streak. stars= how many (600 unless
  // given, 50 to 3000); speed= how fast the ship flies (4, 1 to 20); warp starts it at warp.
  //
  //   {starfield stars=1200, warp/}

  return padToy ( 'starfield', 'Starfield', [
    'stars' => padToyInt ( 'stars', 600, 50, 3000 ),
    'speed' => padToyInt ( 'speed', 4, 1, 20 ),
    'warp'  => padToyFlag ( 'warp' )
  ] );

?>
