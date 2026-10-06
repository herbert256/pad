<?php

  // The same customer number as an integer and as text - a database row beside a CSV row -
  // is one value to group on, as it is one value to dedup.

  $mixed = [ [ 'c' => 5, 'v' => 1 ], [ 'c' => '5', 'v' => 2 ], [ 'c' => 6, 'v' => 3 ] ];

?>
