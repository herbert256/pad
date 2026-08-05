<?php

  // Expression entry for a sequence action: a call inside an evaluated expression whose name
  // matches one of actions/types/, e.g. sum(...) or sort(...). eval/parms/sequence.php has
  // already put the action in $pqSetAction and its arguments in $pqSetParms. Notes the entry
  // point for {info} in $pqSetStart and returns the result as a plain array.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/action.php";

?>