<?php

  // Build strategy 'loop' for the modulo sequence: each term is the loop value modulo the
  // parameter, which defaults to 1. {modulo 3} over 1, 2, 3, ... gives 1, 2, 0, 1, 2, 0,
  // ..., which makes it a handy cycling counter as well as an arithmetic play.

  if ( ! $pqParm )
    $pqParm = 1;

  return $pqLoop % (int) $pqParm;

?>