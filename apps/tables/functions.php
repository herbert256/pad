<?php

  // Every function of the formulas subsystem - the files of pad/subs/formulas/types/ - with
  // what its file says of it and its examples, computed here on the sample sheet.

  $title  = 'Functions';
  $groups = sheetsGroups ();
  $count  = array_sum ( array_map ( fn ( $group ) => count ( $group ['functions'] ), $groups ) );

?>
