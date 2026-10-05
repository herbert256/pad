<?php

  // A record, field or check reads one row, and db() limits the statement to it - unless the
  // statement ends in a limit of its own, which got a second one appended: an SQL syntax
  // error. A limit inside a subquery is no limit of the statement.

  $latest = db ( "record orderNumber from orders order by orderNumber desc limit 1" );
  $second = db ( "field orderNumber from orders order by orderNumber desc limit 1, 1" );
  $third  = db ( "field orderNumber from orders order by orderNumber LIMIT 1 OFFSET 2" );
  $bound  = db ( "field orderNumber from orders order by orderNumber limit {0}", [ 4 ] );
  $check  = db ( "check orders where orderNumber > 10424 limit 1" ) ? 'yes' : 'no';
  $inner  = db ( "field count(*) from (select orderNumber from orders limit 3) as x" );

?>
