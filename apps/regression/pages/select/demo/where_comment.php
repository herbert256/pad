<?php

  // A comment with an apostrophe in where= no longer stops the $name after it from binding:
  // the apostrophe used to open a quoted literal, so $who was spliced raw and the query
  // failed on "Unknown column '$who'".

  $padSelect ['staffWc'] = [ 'db' => 'staff' ];

  $who = 'jim';

?>
