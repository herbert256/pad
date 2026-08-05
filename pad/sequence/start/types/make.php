<?php

  // Type entry for make: reached from types/make.php for the prefixed forms {make:fibonacci}
  // and {fibonacci:make}, which padTypeSeq() accepts either way round. Notes the entry point
  // for {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/type.php";

?>