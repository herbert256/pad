<?php

  // battleship - Local solitaire hidden fleet on size=6..10 (default 8). Ship lengths 4,3,2; horizontal/vertical, no overlap, touching allowed. Seed uses shared deterministic game RNG. Placement enumerates all legal positions rather than retrying indefinitely. Shots reveal miss/hit/sunk; repeat coordinates cost no turn; all nine ship cells wins. Native buttons, arrow keys and typed coordinates support keyboard play. No network/multiplayer.
  //
  // ## Example
  //
  // ```pad
  // {game:battleship size=8, seed=42/}
  // ```
  //
  // Run the focused fixture with `node tests/subsystems/game-battleship.cjs`.

return padGame('battleship','Fleet search',['size'=>padGameInt('size',8,6,10)],'<p>Find the hidden fleet: ships of length 4, 3 and 2. Ships do not overlap and may touch. Choose a coordinate; repeated shots cost no turn.</p>');
