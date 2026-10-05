<?php

  // A RECORD the page's PHP adds a field to, and casts a value of, is still one record:
  // one occurrence, with the new field beside the columns.

  $jim          = db ( "record name, phone from staff where name = 'jim'" );
  $jim ['full'] = 'Jim from the staff';
  $jim ['name'] = ucfirst ( $jim ['name'] );

?>
