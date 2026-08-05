<?php

  // Type entry for remove: reached from types/remove.php for the prefixed forms
  // {remove:prime} and {prime:remove}, which padTypeSeq() accepts either way round. Notes the
  // entry point for {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/type.php";

?>