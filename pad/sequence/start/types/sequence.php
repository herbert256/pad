<?php

  // Type entry for sequence: reached from types/sequence.php when padTypeCommon() resolved a
  // bare tag name to a directory in types/, so {prime} or {fibonacci} runs as a sequence.
  // Notes the entry point for {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/type.php";

?>