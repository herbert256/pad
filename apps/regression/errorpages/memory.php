<?php

  // A page that runs out of memory: the report on disk prints the globals bounded, so the
  // visitor still gets _errors/500.pad with the request id - printed whole, the data that
  // filled the memory filled it again and the answer was an empty 500.

  ini_set ( 'memory_limit', '64M' );

  $filled = [];

  while ( TRUE )
    $filled [] = str_repeat ( 'x', 1000000 );

?>
