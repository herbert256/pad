<?php

  // Fires from level/var.php once the name and the pipe of a variable tag - {$x}, {!x},
  // {#x}, {&x}, {?x}, {^x} - are split, and traces them when $padInfoTraceVar is on: the
  // name as written ({$$x} shows $x, before the indirection) and the pipe expression.

  global $padInfoTrace;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceVar )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'var', 'start', 'var=' . $padFld . ' options=' . padJson($padVarOpts) );

?>
