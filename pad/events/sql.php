<?php

  global $padInfoTrace, $padInfoTraceSql;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceSql )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'sql', 'info', [ 'input' => $input, 'vars' => $vars, 'sql' => $sql, 'result' => $return ] );

?>
