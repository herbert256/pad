<?php

  // Tag entry for {sequence}: the general form, where the type, store or action is named by
  // a parameter. Reached from tags/sequence.php; notes the entry point for {info} in
  // $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/tag.php";

?>