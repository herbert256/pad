<?php

  if ( ! $GLOBALS ['padTraceTypes'] ['sql'] )
    return;

  if ( $GLOBALS ['padTraceTree'] ) {

    padTrace ( 'sql', 'file', $input ); 

    $file = "sql-" . $GLOBALS ['padTrace'];
    $data = [ 'input' => $input, 'vars' => $vars, 'sql' => $sql, 'result' => $return ];

    padTraceFile ( $file, 'json',  $data );

  }

  else {

    padTrace ( 'sql', 'start', $input ); 
    padTrace ( 'sql', 'end',   $return ); 

  }

?>