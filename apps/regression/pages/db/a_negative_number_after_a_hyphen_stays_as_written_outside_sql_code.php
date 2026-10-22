<?php

  // A negative number spliced right after a minus gets a space before it in the SQL a tag
  // writes, outside its quoted literals, where 2000--1000 is a line comment on SQLite - and
  // nowhere else: the page's text, an attribute, an echo and a literal keep the value as it is,
  // a literal in the fields= or join= of a select too, where no other value may stand.

  $lo  = -12;
  $hi  = -4;
  $id  = -1;
  $adj = -1000;

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name' ];

?>
