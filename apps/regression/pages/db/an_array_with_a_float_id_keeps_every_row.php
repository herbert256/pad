<?php

  // The rows of an ARRAY are keyed by their id column when it can be a key; a floating
  // point id - a PHP float since the drivers answer numbers - ended the request on "Implicit
  // conversion from float 1.5 to int loses precision", and 1.2 and 1.7 would share key 1.

  $rows = db ( "array 1.2e0 as id, 'a' as x union all select 1.7e0, 'b' union all select 2.5e0, 'c'" );

  $keys = implode ( ' ', array_keys ( $rows ) );

?>
