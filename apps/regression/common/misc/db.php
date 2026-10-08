<?php

  // The one test that queries the database, so the demo credentials _common's .env
  // supplies are proven to work - the db tests in regression/pages carry their own.

  $dbPhone = db ("field phone from staff where name = 'jim'");

?>
