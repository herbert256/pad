<?php

  // A negative number spliced right after a minus gets a space before it in the SQL a tag
  // writes, outside its quoted literals, where 2000--1000 is a line comment on SQLite - and
  // nowhere else: the page's text, an attribute, an echo and a literal keep the value as it is.

  $lo  = -12;
  $hi  = -4;
  $id  = -1;
  $adj = -1000;

?>
