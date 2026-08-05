<?php

  // Closing counterpart of events/var_start.php: traces the finished value of a {$var}
  // substitution when $padInfoTraceVar is on. Nothing includes this file at present; $padVal
  // is the value level/var.php hands to padLevel().

  global $padInfoTrace;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceVar )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'var', 'end', 'value=' . $padVal );

?>