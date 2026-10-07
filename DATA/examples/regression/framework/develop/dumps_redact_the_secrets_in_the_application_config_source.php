<?php

  // An error report shows the globals, and $padConfigApp held the whole source of the
  // application's _config/config.php - where the database password and the application key
  // are written: the reports printed both in clear beside the redacted globals of the same
  // names. The source is read for what it needs and kept in no global a report shows.

  $kept = array_key_exists ( 'padConfigApp', $GLOBALS ) ? 'a global' : 'in no global';

?>
