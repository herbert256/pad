<?php

  // Tag entry for {pull}: iterate a sequence that an earlier push= stored, or the last one
  // pushed when no name is given. Reached from tags/pull.php; notes the entry point for
  // {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/tag.php";

?>