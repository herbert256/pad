<?php

  // Traces the start of a {$var} substitution - the field name and the pipe expression that
  // follows it - when $padInfoTraceVar is on. Nothing includes this file at present; the
  // variables it reads are set at the top of level/var.php.

  global $padInfoTrace;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceVar )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'var', 'start', 'var=' . $padFld . ' options=' . padJson($padVarOpts) );

?>