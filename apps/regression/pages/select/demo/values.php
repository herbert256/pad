<?php

  // A key value bound on a declared select table reaches the SQL as a value, whatever it
  // holds: a comma is still a comma and matches, and a quote cannot end the string and
  // become part of the condition. Over the customers table of the demo database.

  $padSelect ['named'] = [ 'db' => 'customers', 'key' => 'customerName' ];

?>
