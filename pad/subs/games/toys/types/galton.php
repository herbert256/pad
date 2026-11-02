<?php

  // galton - Galton's board: balls drop through rows of pegs, bouncing left or right at each
  // one by chance, and pile up in the bins below into the bell of the normal distribution -
  // drawn over them for the count dropped. Drop one ball, a hundred, or let them pour; tilt
  // the board to make every bounce lean one way. levels= the rows of pegs (12 unless given, 4
  // to 20); balls= how many the board pours before it stops (500, 10 to 5000); bias= the
  // chance in percent that a ball goes right (50, 5 to 95).
  //
  //   {toy:galton levels=16, bias=65/}

  return padToy ( 'galton', 'Galton board', [
    'levels' => padToyInt ( 'levels', 12, 4, 20 ),
    'balls'  => padToyInt ( 'balls', 500, 10, 5000 ),
    'bias'   => padToyInt ( 'bias', 50, 5, 95 )
  ] );

?>
