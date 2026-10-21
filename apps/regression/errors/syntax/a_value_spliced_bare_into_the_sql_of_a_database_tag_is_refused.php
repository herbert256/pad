<?php

  // A value spliced into the SQL of a database tag outside a quoted literal of it is SQL: a
  // request value 0 union select user() read any table, and a single word - this one - made
  // name = name and listed every row. It goes in as $n, bound as an escaped literal.

  $n = 'name';

?>
