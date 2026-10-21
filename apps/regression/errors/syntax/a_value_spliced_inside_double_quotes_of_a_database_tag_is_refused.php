<?php

  // Only a '...' literal holds a value as text: "..." is a name on SQLite and on MySQL under
  // ANSI_QUOTES, so name = "{$who}" with this value picked the column and every row matched.

  $who = 'name';

?>
