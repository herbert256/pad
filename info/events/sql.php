<?php

  if ( ! $GLOBALS ['padInfoTrace'] )
    return;

  if ( ! $GLOBALS ['padInfoTrace'] or ! $GLOBALS ['padInfoTraceSql'] )
    return;

 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'sql', 'info', [ 'input' => $input, 'vars' => $vars, 'sql' => $sql, 'result' => $return ] );

?>