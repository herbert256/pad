<?php

  // Build strategy 'fixed': the type's own fixed.php returns a ready-made term list.
  // Puts it in $pqFixed and hands it to the fixed iterator.

  $pqFixed = include PT . "$pqSeq/fixed.php";

  include PQ . 'build/types/type/fixed.php';

?>