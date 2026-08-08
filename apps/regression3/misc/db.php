<?php

  // The one test that queries the database, so the demo credentials _common/_config
  // supplies are proven to work - the db tests in regression2 carry their own.

  $dbPhone = db ("field phone from staff where name = 'jim'");

?>