<?php

  // maze - find the way through a maze from its top left corner to the flag at the bottom
  // right, with the arrow keys, by swiping or with the buttons under it. Every maze has one
  // way through - a new one is dug each game, the same one for the same seed=. height= and
  // width= its size in cells, 12 by 18 unless given (5 to 40).

  return padGame ( 'maze', 'Maze', [
    'height' => padGameInt ( 'height', 12, 5, 40 ),
    'width'  => padGameInt ( 'width', 18, 5, 40 )
  ] );

?>
