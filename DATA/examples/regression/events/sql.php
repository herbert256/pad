<?php

  // One statement through db(): the sql hook hears it as it was sent to the database.

  $count = db ( "field count(*) from staff" );
  $heard = $GLOBALS ['heardSql'] [0] ?? 'nothing';

?>
