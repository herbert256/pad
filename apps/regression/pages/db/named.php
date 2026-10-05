<?php

  // A _data/*.sql file is a named query: {staffByPhone} runs it, and each {$field} in it is
  // bound as a placeholder - this hostile pattern compares as a value, it never joins the
  // statement.

  $pattern = '%4%';
  $max     = 10;
  $hostile = "%' or '1'='1";

?>
