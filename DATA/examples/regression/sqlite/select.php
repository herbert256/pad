<?php

  // The Select subsystem over the same staff table: the statement it builds - backquoted
  // names, a quoted key, limit offset,count - is one SQLite reads as MySQL does.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name' ];

  // A union member with rows= of its own keeps them: SQLite takes no member in parentheses,
  // so it is a derived table there.

  $padSelect ['topPaid']  = [ 'db' => 'staff', 'fields' => 'name', 'order' => 'salary desc', 'rows' => 1 ];

  // A value bound into where= is escaped as SQLite wants it: the quote stays in the string.

  $who = "x' or name > '";

  // A negative number bound after a minus keeps its distance, so "salary > 2000-$adj" is
  // 2000 - (-1000) = 3000 and not a -- comment that leaves SQLite with half a clause - and
  // so does one spliced there as {$adj}, in a select's where= and in a {pad:field}.
  //
  // distinctrow is MySQL's word for distinct, which SQLite reads as a column name: the select
  // writes distinct there.

  $adj = -1000;

?>
