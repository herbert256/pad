<?php

  // Tag entry for {flag}: replace each value with 1 or 0 according to the named sequence
  // type's test, keeping every value instead of filtering. Reached from tags/flag.php; notes
  // the entry point for {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/tag.php";

?>