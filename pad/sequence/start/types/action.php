<?php

  // Type entry for action: reached from types/action.php both for {action:sum} and for a bare
  // {sum}, since padTypeCommon() resolves any name in actions/types/ to 'action'. Notes the
  // entry point for {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/type.php";

?>