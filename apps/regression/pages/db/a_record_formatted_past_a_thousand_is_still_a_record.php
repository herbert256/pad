<?php

  // A RECORD whose numbers the page formats for show stays one row whatever their size:
  // number_format writes 10000 as '10,000.00', no longer loosely the 10000 db() answered,
  // so a record of totals was one row while they stayed below a thousand and fell apart the
  // day they passed it. A number grouped either way, or rounded to its decimals, is still the
  // record's own; a heading list of the same columns still stays a list.

  $totals = db ( "record sum(salary) as total, avg(salary) as average from staff" );
  $totals = array_map ( fn ( $value ) => number_format ( (float) $value, 2 ), $totals );

  $dutch = db ( "record sum(salary) as total, avg(salary) as average from staff" );
  $dutch = array_map ( fn ( $value ) => number_format ( (float) $value, 2, ',', '.' ), $dutch );

  $thirds = db ( "record sum(salary) / 3 as third, avg(salary) / 3 as part from staff" );
  $thirds = array_map ( fn ( $value ) => number_format ( (float) $value, 1 ), $thirds );

  $labels = [ 'third' => 'Third', 'part' => 'Part' ];

?>
