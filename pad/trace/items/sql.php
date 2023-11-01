<?php

  if ( ! $GLOBALS ['padTraceItems'] ['sql'] )
    return;

  padTrace ( 'sql', 'file', $input ); 

  padTraceFile ( 'sql', 'json', [ 'input' => $input, 'vars' => $vars, 'sql' => $sql, 'result' => $return ] );

?>