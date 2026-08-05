<?php

  // Type entry for keep: reached from types/keep.php for the prefixed forms {keep:prime} and
  // {prime:keep}, which padTypeSeq() accepts either way round. Notes the entry point for
  // {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/type.php";

?>