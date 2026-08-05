<?php

  // Build strategy 'build' for the list sequence: the whole term list is the parameter split
  // on semicolons, so {list '5;2;8;1;9'} iterates those five values in that order.
  // padGetList() casts the numeric entries to int and leaves anything else as a string;
  // build/types/build.php then walks the result like any other ready-made list.

  return padGetList ( $pqParm );

?>