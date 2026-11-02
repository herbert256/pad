<?php

  // voronoi - Exact Euclidean nearest-site cells from iterative half-plane clipping, capped at 32 sites. Drag sites, change their number, regenerate or pause. Seeded sites use the shared toy random generator. Shared lifecycle pauses off-screen/hidden/removed toys and starts with a static scene under reduced motion. Each site has a visible number; keyboard users can use labelled site-count, reset and Play controls.
  //
  // `{toy:voronoi seed=17, paused/}`. Shared theme colours, CSP loading and responsive canvas apply.

return padToy('voronoi','Moving Voronoi cells',['points'=>padToyInt('points',12,2,32)]);
