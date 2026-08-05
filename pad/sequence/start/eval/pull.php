<?php

  // Expression entry for pulling a stored sequence, kept apart from start/eval/action.php so
  // {info} can tell the two apart. Nothing routes here at present: eval/parms/sequence.php
  // sends both actions and pulls to start/eval/action.php.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/action.php";

?>