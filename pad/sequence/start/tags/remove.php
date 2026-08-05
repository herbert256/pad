<?php

  // Tag entry for {remove}: drop the values the named sequence type accepts, the inverse of
  // {keep}. Reached from tags/remove.php; notes the entry point for {info} in $pqSetStart
  // and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/tag.php";

?>