<?php

  // Build strategy 'loop' for the modulo sequence: each term is the loop value modulo the
  // parameter, which defaults to 1. {modulo 3} over 1, 2, 3, ... gives 1, 2, 0, 1, 2, 0,
  // ..., which makes it a handy cycling counter as well as an arithmetic play.

  // fmod rather than %, so the parameter is used as it was written rather than cut down to a
  // whole number - cutting it down turned any parameter below 1 into a modulo of zero. A
  // whole parameter still gives whole answers.

  if ( ! $pqParm )
    $pqParm = 1;

  return fmod ( $pqLoop, $pqParm );

?>