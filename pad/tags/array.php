<?php

  // {array "* from users"} queries the database and returns every row as this level's
  // data, one occurrence per row. Shares tags/record.php, which passes the tag name to
  // db() as the command word - which is why the parameter starts after the SELECT.

  return include PAD . 'tags/record.php';

?>