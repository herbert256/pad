<?php

  // The content between the tags is a line over the grid - a name, a date to fill in - in a box
  // of 9 mm ending 2 mm above it, its text at the bottom, the grid moved down for it: ruled
  // paper of 8 mm on A4 has 277 mm of room inside its margins, 265 with the line, 33 lines
  // apart - 264 mm - from 22.5 mm down; without content it starts at 12.5. A sheet for every
  // row of the data, the line of each its own.

  $rows   = [ [ 'name' => 'Ann' ], [ 'name' => 'Bob' ] ];
  $answer = printablesShow ( "{printable:graphpaper grid='lines', data='rows', size=11}Name: {\$name}{/printable:graphpaper}", 3 )
          . ' ## ' . printablesShow ( "{printable:graphpaper grid='lines'/}", 2 );

?>
