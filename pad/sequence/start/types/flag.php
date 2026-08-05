<?php

  // Type entry for flag: reached from types/flag.php for the prefixed forms {flag:prime} and
  // {prime:flag}, which padTypeSeq() accepts either way round. Notes the entry point for
  // {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/type.php";

?>