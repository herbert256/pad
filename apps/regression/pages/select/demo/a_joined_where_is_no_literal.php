<?php

  // where= must be one quoted literal too: 'name = 1 or ' . $cond joined a condition from a
  // variable into the SQL, and with this one every row came back.

  $padSelect ['staffJoinW'] = [ 'db' => 'staff' ];

  $cond = '1=1 or name = name';

?>
