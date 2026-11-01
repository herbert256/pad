<?php

  // An editable spreadsheet of 30 x 30 numbers in a {live} region is one form the live script
  // posts, which a change of a cell submits: every cell's text stands once, in its field, and
  // the page grows with the cells - some 115 KB, as outside a region. Each of the 900 fields
  // carried the texts of all the others in its pad-value, and the page was 10.7 MB.

  $grid = [];

  for ( $row = 0; $row < 30; $row++ )
    for ( $column = 0; $column < 30; $column++ )
      $grid [$row] [$column] = ( $row * 30 + $column ) * 1.5;

?>
