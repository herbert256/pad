<?php

  // A REAL or DOUBLE comes back from the database as a PHP float, and bound back - a db()
  // placeholder, a $name of a database tag - it was written with PHP's 14 significant
  // digits, 1234567.8912346, and no longer found its own row; 0.1 + 0.2 went in as 0.3.
  // A whole float stays a count after LIMIT.

  $k     = db ( "field 1234567.891234567e0 as k" );
  $sum   = db ( "field 0.1e0 + 0.2e0 as k" );
  $three = db ( "field 3.0e0 as k" );

  $same  = db ( "field {0} = 1234567.891234567e0", [ $k ] );
  $added = db ( "field {0} = 0.1e0 + 0.2e0", [ $sum ] );
  $limit = count ( db ( "array name from staff order by name limit {0}", [ $three ] ) );

?>
