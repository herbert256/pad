<?php

  // The real page: its rows come from the database.

  $title = 'Staff';
  $staff = db ( 'array name from staff order by name limit 2' );

?>
