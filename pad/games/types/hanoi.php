<?php

  // hanoi - the towers of Hanoi: a stack of discs, the largest at the bottom, moves from the
  // first peg to the last, one disc at a time and never a larger disc on a smaller one. Click
  // a peg to lift its top disc and another to put it down. disks= how many, 4 unless given
  // (3 to 8); the fewest moves it can be done in is two to that power, less one.

  return padGame ( 'hanoi', 'Towers of Hanoi', [
    'disks' => padGameInt ( 'disks', 4, 3, 8 )
  ] );

?>
