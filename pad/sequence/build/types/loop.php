<?php

  // Build strategy 'loop': the type's own loop.php decides what the current loop value
  // contributes - a term, or FALSE to reject it. The loop iterator walks the range and
  // build/one.php includes that file per candidate.

  include PQ . 'build/types/type/loop.php';

?>