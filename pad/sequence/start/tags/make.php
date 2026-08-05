<?php

  // Tag entry for {make}: replace each generated value with the one the named sequence type
  // makes from it. Reached from tags/make.php; notes the entry point for {info} in
  // $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/tag.php";

?>