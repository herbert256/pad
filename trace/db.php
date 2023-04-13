<?php
 
    padDbTrace ($db_type, $padSqlStart, $padDbRowsFound, $sql) ;

    global $padSqlCnt;

    $padSqlCnt++

    $backTrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS,3);
    extract ( $backTrace[2] );

    $trace = [
      'type'   => $db_type,
      'sql'    => $sql, 
      'vars'   => $vars,
      'format' => $GLOBALS ['padLastSql'],
      'time'   => hrtime(true) - $padSqlStart,
      'rows'   => $padDbRowsFound,
      'file'   => $file,
      'line'   => $line
      'result' => $return
    ];

    padFilePutContents ( $GLOBALS ['padTraceDir'] . "/sql-$padSqlCnt.json", $trace  );
    
?>