<?php

  // The verb of a statement ends at any white space: a newline or a tab after it, as a
  // named query or a statement written over several lines has it, is the same verb.

  $wsArray  = db ( "select\nname from staff order by name limit 2" );
  $wsRecord = db ( "record\tname from staff order by name" );
  $wsField  = db ( "field\nname from staff order by name" );
  $wsCheck  = db ( "check\nstaff where name = 'jim'" );

?>
