<?php

  // The placeholders on SQLite: a quote is doubled there, never backslashed - the MySQL
  // escape would end the literal early and let the rest join the statement.

  $bare    = db ( "field count(*) from staff where salary > {0}",  [ '99999 or 1=1' ] );
  $number  = db ( "field count(*) from staff where salary > {0}",  [ 0 ] );
  $quoted  = db ( "field count(*) from staff where name = '{0}'",  [ "jim' or 'a'='a" ] );
  $slashed = db ( "field count(*) from staff where name = '{0}'",  [ "jim\\' or 1=1 --" ] );
  $double  = db ( "field count(*) from staff where name = \"{0}\"", [ 'jim" or "a"="a' ] );
  $list    = db ( "field count(*) from staff where name in ({0})", [ [ 'jim', 'bob' ] ] );
  $twice   = db ( "field '{0}' || '|' || {1}",                     [ '{1}', 'b' ] );
  $irish   = db ( "field {0} || ''",                               [ "O'Brien" ] );

  // A bare negative number after a minus keeps its distance: "10-{0}" with -5 is 15, not a
  // -- comment that leaves SQLite with "10" alone.

  $minus   = db ( "field 10-{0}",                                  [ -5 ] );

?>
