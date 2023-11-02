<?php

  if ( ! $GLOBALS ['padTraceSql'] )
    return;

  padTrace ( 'sql', 'file', $input ); 

  padTraceFile ( 'sql', 'json', [ 'input' => $input, 'vars' => $vars, 'sql' => $sql, 'result' => $return ] );

?>