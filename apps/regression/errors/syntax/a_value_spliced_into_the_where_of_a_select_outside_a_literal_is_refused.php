<?php

  // where= of a select table is SQL: a value spliced into it outside a '...' literal is
  // refused, as in a database tag. Taken for the expression where= is on other tags, it was
  // quoted in "...", a name on SQLite: name = "name" matched every row.

  $padSelect ['staffWhere'] = [ 'db' => 'staff' ];

  $pick = 'name';

?>
