<?php

  // Fires from db() in lib/db.php after a query has run and its result has been shaped, and
  // logs the caller's input, the variables bound into it, the SQL actually sent and the
  // returned value as one 'sql' trace entry when $padInfoTraceSql is on.

  global $padInfoTrace, $padInfoTraceSql;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceSql )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'sql', 'info', [ 'input' => $input, 'vars' => $vars, 'sql' => $sql, 'result' => $return ] );

?>