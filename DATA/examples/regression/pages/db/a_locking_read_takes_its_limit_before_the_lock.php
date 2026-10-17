<?php

  // A record, field or check reads one row, and the limit db() adds for it goes in front of
  // a locking clause that ends the statement, before a semicolon and on a line after a line
  // comment: it was appended to the statement as written, "... for update limit 0,1" and
  // "...; limit 0,1" were SQL syntax errors, and after "-- any" the limit was part of the
  // comment, so a check answered how many rows there were.

  $record = db ( "record name from staff where name = {0} for update", [ 'bob' ] );
  $field  = db ( "field phone from staff where name = {0} lock in share mode", [ 'jim' ] );
  $semi   = db ( "field name from staff where name = 'joe';" );
  $check  = db ( "check staff where salary > 0 -- any" );
  $own    = db ( "field name from staff order by name limit 1, 1 for update" );

?>
