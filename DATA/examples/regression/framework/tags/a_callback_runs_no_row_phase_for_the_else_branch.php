<?php

  // An empty level and one its where= emptied show their @else@: that branch is no row, so
  // the callback's row phase does not run for it - init and exit do. The row phase read the
  // row's fields and ended the request ("Undefined variable $n").

  $xs = [ [ 'n' => 1 ], [ 'n' => 2 ], [ 'n' => 3 ] ];

?>
