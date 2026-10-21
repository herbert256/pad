<?php

  // having= is SQL the template writes, and a value spliced into it outside a quoted literal
  // is SQL: with this one every group came back. It goes in as $n, bound as a literal.

  $padSelect ['staffHaving'] = [ 'db' => 'staff' ];

  $n = '5 or 1';

?>
