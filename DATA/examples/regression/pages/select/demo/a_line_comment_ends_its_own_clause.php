<?php

  // A -- comment written in a clause - where=, group=, having=, order= - ends that clause:
  // the clauses of the statement stand on lines of their own. On one line the comment ran to
  // its end, so the having, the order and the limit after it were gone without a word, and
  // the closing parenthesis of the where with them. Over the staff table of the demo database.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];

?>
