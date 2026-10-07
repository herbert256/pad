<?php

  // fields= must be one quoted literal: an expression that only starts with one - '' . $cols -
  // joins a variable into the SQL, and with $cols = "phone as name" it answered every phone
  // number, as fields=$cols did.

  $padSelect ['staffJoinF'] = [ 'db' => 'staff' ];

  $cols = 'phone as name';

?>
