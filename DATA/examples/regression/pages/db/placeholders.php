<?php

  // A bare placeholder takes a number as a number and quotes anything else, so '99999 or 1=1'
  // compares as a value instead of joining the statement. Inside quotes a value is escaped,
  // an array written bare becomes a list, and a value holding {1} is not filled again.

  $bare     = db ( "field count(*) from staff where salary > {0}",          [ '99999 or 1=1' ] );
  $number   = db ( "field count(*) from staff where salary > {0}",          [ 0 ] );
  $quoted   = db ( "field count(*) from staff where name = '{0}'",          [ "jim' or 'a'='a" ] );
  $list     = db ( "field count(*) from staff where name in ({0})",         [ [ 'jim', 'bob' ] ] );
  $twice    = db ( "field concat('{0}', '|', {1})",                         [ '{1}', 'b' ] );

?>
