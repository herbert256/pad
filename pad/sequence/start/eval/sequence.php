<?php

  // Expression entry for a sequence type: a call inside {echo ...} or any evaluated
  // expression whose name matches a directory in types/. eval/parms/sequence.php has already
  // put the name in $pqSetAction and the call's arguments in $pqSetParms. Notes the entry
  // point for {info} in $pqSetStart and returns the values as a plain array.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/sequence.php";

?>