<?php

  // A field piped into a fallback - default(), ??, optional - may be missing under the strict
  // check in {echo} and inside an expression too, not only in {$x | ...}: they named it missing.

  $user = [ 'name' => 'Ann' ];
  $xs   = [ [ 'n' => 1 ], [ 'n' => 2, 'm' => 5 ] ];

?>
