<?php

  // A RECORD stays one row when the page formats every value it shows - the totals given a comma and
  // two decimals, a name given a capital - and when none of its columns holds a value: it
  // has the columns of the row db() answered, in their order. It was known only by a column
  // still holding the answered value, so each became one occurrence per column, holding its
  // value under the tag's name, and {$total} was not found.

  $totals = db ( "record sum(salary) as total, avg(salary) as average from staff" );
  $totals = array_map ( fn ( $value ) => number_format ( (float) $value, 2 ), $totals );

  $bob           = db ( "record name from staff where name = 'bob'" );
  $bob ['name']  = ucfirst ( $bob ['name'] );

  $profile = db ( "record '' as bio, NULL as website from staff where name = 'bob'" );

?>
