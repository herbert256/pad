<?php

  // type= is the statement's command word, not free SQL: a value that tries to be a whole
  // query - the kind request input could set - is refused, where it was spliced raw.

  $padSelect ['staffType'] = [ 'db' => 'staff' ];

  $evil = "array name from customers limit 1 #";

?>
