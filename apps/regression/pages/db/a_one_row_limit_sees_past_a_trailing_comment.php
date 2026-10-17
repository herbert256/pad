<?php

  // A record, field or check whose statement ends in a line comment gets its one-row limit
  // judged and placed as if the comment were not there: its own limit before the comment is
  // seen, a semicolon or a locking clause before it is too. The tests ran on the text with
  // the comment, so "limit 1 -- the one row" got a second limit on a line after the comment,
  // as did "'bob'; -- the one": MySQL syntax errors near 'limit 0,1'.

  $count  = db ( "field count(*) from staff where name = 'bob' limit 1 -- the one row" );
  $first  = db ( "record name from staff order by name limit 1 # first" );
  $check  = db ( "check staff where salary > 0 limit 1 -- any" );
  $semi   = db ( "field name from staff where name = 'bob'; -- the one" );
  $locked = db ( "field phone from staff where name = 'jim' for update -- lock it" );
  $plain  = db ( "check staff where salary > 0 -- any" );

?>
