<?php

  // Build strategy 'make': the type's own make.php derives a term from the current loop
  // value (nudging it onto the sequence rather than testing it). The loop iterator walks
  // the range and build/one.php includes that file per candidate.

  include PQ . 'build/types/type/loop.php';

?>