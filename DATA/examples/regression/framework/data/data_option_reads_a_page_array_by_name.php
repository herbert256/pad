<?php

  // data='crew' names the page's array as {chart data='crew'} and lookup() do - it read
  // the word as a CSV header with no rows and iterated nothing. A value naming nothing
  // still names nothing in silence.

  $crew = [ [ 'name' => 'Ann', 'salary' => 10 ], [ 'name' => 'Bob', 'salary' => 30 ] ];
  $v    = 'nosuch';

?>
