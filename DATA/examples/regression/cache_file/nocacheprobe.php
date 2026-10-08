<?php

  // The fixture of nocache: its build moment in nanoseconds, and rows for a loop. A hit
  // of the page cache runs none of this - the {nocache} parts render without it.

  $stamp = hrtime ( TRUE );
  $rows  = [ [ 'v' => 1 ], [ 'v' => 2 ] ];

?>
