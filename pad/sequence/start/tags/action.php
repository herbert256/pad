<?php

  // Tag entry for {action}: apply one of actions/types/ - sum, sort, reverse, first and the
  // rest - to a sequence's values. Reached from tags/action.php; notes the entry point for
  // {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/tag.php";

?>