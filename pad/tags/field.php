<?php

  // {field "count(*) from users"} queries the database for a single value and prints it.
  // Shares tags/record.php, which passes the tag name to db() as the command word - which
  // is why the parameter starts after the SELECT.

  return include PAD . 'tags/record.php';

?>