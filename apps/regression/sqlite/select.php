<?php

  // The Select subsystem over the same staff table: the statement it builds - backquoted
  // names, a quoted key, limit offset,count - is one SQLite reads as MySQL does.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name' ];

  // A value bound into where= is escaped as SQLite wants it: the quote stays in the string.

  $who = "x' or name > '";

  // A negative number bound after a minus keeps its distance, so "salary > 2000-$adj" is
  // 2000 - (-1000) = 3000 and not a -- comment that leaves SQLite with half a clause.

  $adj = -1000;

?>
