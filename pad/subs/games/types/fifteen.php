<?php

  // fifteen - the sliding puzzle: numbered tiles in a frame with one gap, shuffled by sliding;
  // slide a tile into the gap - click it, or use the arrow keys - until they stand in order.
  // size= the frame, 4 by 4 unless given (3 to 6).

  return padGame ( 'fifteen', 'Fifteen puzzle', [
    'size' => padGameInt ( 'size', 4, 3, 6 )
  ] );

?>
