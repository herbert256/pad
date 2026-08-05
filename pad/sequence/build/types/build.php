<?php

  // Build strategy 'build': the type's own build.php returns its whole term list, computed
  // in one go from the current parameters. Puts it in $pqFixed and hands it to the fixed
  // iterator.

  $pqFixed = include PT . "$pqSeq/build.php";

  include PQ . 'build/types/type/fixed.php';

?>