<?php

  // Tag entry for {keep}: keep only the values the named sequence type accepts. Reached from
  // tags/keep.php; notes the entry point for {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/tag.php";

?>