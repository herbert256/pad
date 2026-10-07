<?php

  $title = 'Syntax Highlighting';

  // A query as it would come from a database or a log - a value, coloured by the pipe.

  $query = "SELECT name, COUNT(*) AS orders FROM customers JOIN orders USING (customer_id) WHERE country = 'NL' GROUP BY name";

?>
