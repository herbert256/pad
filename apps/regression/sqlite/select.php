<?php

  // The Select subsystem over the same staff table: the statement it builds - backquoted
  // names, a quoted key, limit offset,count - is one SQLite reads as MySQL does.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name' ];

  // A value bound into where= is escaped as SQLite wants it: the quote stays in the string.

  $who = "x' or name > '";

?>
