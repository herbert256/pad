<?php

  // Type entry for pull: reached from types/pull.php both for {pull:mySeq} and for a bare
  // {mySeq}, since padTypeCommon() resolves any name present in $pqStore to 'pull'. Notes the
  // entry point for {info} in $pqSetStart and runs the tag pipeline.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/type.php";

?>