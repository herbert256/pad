<?php

  // pendulum - a double pendulum: a second pendulum hung from the end of the first, the
  // classic chaotic system - a start a hundredth of a degree away ends somewhere else
  // entirely, which a few of them side by side show. Drag a bob to hold it and let go to
  // start from there; the lower bob leaves a fading trail. pendulums= how many swing side by
  // side, each started a little away from the one before (1 unless given, up to 8); angles=
  // the start of the two arms in degrees from straight down, '120, -10' unless given;
  // trail= the points of the trail (400, 0 to 3000); gravity= in metres a second squared
  // (9.81, 1 to 30).
  //
  //   {pendulum pendulums=3, angles='135, 135'/}

  return padToy ( 'pendulum', 'Double pendulum', [
    'pendulums' => padToyInt ( 'pendulums', 1, 1, 8 ),
    'angles'    => padToyNumbers ( 'angles', [ 120, -10 ], -180, 180, 2, 2 ),
    'trail'     => padToyInt ( 'trail', 400, 0, 3000 ),
    'gravity'   => padToyNumber ( 'gravity', 9.81, 1, 30 )
  ] );

?>
