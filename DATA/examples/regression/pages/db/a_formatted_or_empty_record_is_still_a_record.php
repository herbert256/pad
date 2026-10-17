<?php

  // A RECORD whose columns all came back empty stays one row; one whose every value the page
  // formats - the totals given a comma and two decimals, a name a capital - is handed over as
  // a list of one row, [ $totals ], and is that row; and a heading list holding exactly a
  // record's column names in their order, [ 'name' => 'Name', 'salary' => 'Salary' ], stays
  // a list of its values: taken for the record, {$labels} inside it ended the page.

  $totals = db ( "record sum(salary) as total, avg(salary) as average from staff" );
  $totals = [ array_map ( fn ( $value ) => number_format ( (float) $value, 2 ), $totals ) ];

  $bob           = db ( "record name, salary from staff where name = 'bob'" );
  $bob ['name']  = ucfirst ( $bob ['name'] );
  $bob           = [ $bob ];

  $labels = [ 'name' => 'Name', 'salary' => 'Salary' ];

  $profile = db ( "record '' as bio, NULL as website from staff where name = 'bob'" );

?>
