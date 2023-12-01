<?php

  if ( ! $GLOBALS ['padTraceSql'] )
    return;

  padTrace ( 'sql', 'info', [ 'input' => $input, 'vars' => $vars, 'sql' => $sql, 'result' => $return ] );

?>