<?php

  // The whole MySQL layer. db() runs against the application database configured in
  // _config/config.php; padDb() against PAD's own database ($padSqlPad* - sessions,
  // links, logs). Both connect lazily and cache the connection in a global; both return
  // FALSE outright when mysqli is not installed, which is how PAD runs without a database.
  //
  // padDbConnect connects and puts MySQL in TRADITIONAL sql_mode, so bad data errors
  // instead of being silently truncated.
  //
  // padDbPart2 does the work and gives PAD's SQL its shape:
  //   - {0}, {1} ... placeholders are replaced from $vars, escaped, unless the key starts
  //     with x (then it is inserted raw); {0:20} truncates to that length first
  //   - the leading verb decides the shape of the result: CHECK (rowcount, note the
  //     "CHECK table WHERE ..." form), FIELD (one scalar), RECORD (one assoc row), ARRAY
  //     or SELECT (rows, keyed by an id column when the query returns one), INSERT (the
  //     new id, else the row count), UPDATE/DELETE/REPLACE/SET/TRUNCATE/LOAD (row count)
  //   - every statement is appended to $_SQL and the row count left in $padDbRowsFound;
  //     failures go through padError, and events/sql.php logs the query when info is on

  function db ( $sql, $vars = [] ) {

    if ( ! function_exists ( 'mysqli_connect' ) )
      return FALSE;

    global $padSqlConnect, $padSqlHost, $padSqlUser, $padSqlPassword, $padSqlDatabase;

    if ( ! isset ( $padSqlConnect ) )
      $padSqlConnect = padDbConnect ( $padSqlHost, $padSqlUser, $padSqlPassword, $padSqlDatabase );

    return padDbPart2 ( $padSqlConnect, $sql, $vars );

  }

  function padDb ( $sql, $vars = [] ) {

    if ( ! function_exists ( 'mysqli_connect' ) )
      return FALSE;

    global $padSqlPadConnect, $padSqlPadHost , $padSqlPadUser , $padSqlPadPassword , $padSqlPadDatabase;

    if ( ! isset ( $padSqlPadConnect ) )
      $padSqlPadConnect = padDbConnect ( $padSqlPadHost , $padSqlPadUser , $padSqlPadPassword , $padSqlPadDatabase );

    return padDbPart2 ($padSqlPadConnect, $sql, $vars);

  }

  function padDbConnect ( $host, $user, $password, $database ) {

    $connect = mysqli_connect ( "$host" , $user , $password , $database );

    if ( ! $connect )
      return padError ( mysqli_connect_errno ( ) . ' - ' . mysqli_connect_error ( ) );

    mysqli_query($connect, "SET SESSION sql_mode = 'TRADITIONAL'");

    return $connect;

  }

  function padDbPart2 ( $padSqlConnect, $sql, $vars ) {

    global $_SQL, $pad, $padDataSetRecord, $padDbRowsFound, $padInfo, $padPrm;

    $input = $sql;

    foreach ( $vars as $i => $replace ) {

      $pad1 = strpos($sql, '{'.$i.'}' );

      if ( $pad1 !== FALSE )
        if (substr($i, 0, 1) != 'x')
          $sql = str_replace('{'.$i.'}', mysqli_real_escape_string($padSqlConnect, $replace), $sql);
        else
          $sql = str_replace('{'.$i.'}', $replace, $sql);

      $pad1 = strpos($sql, '{'.$i.':' );

      if ($pad1 !== FALSE) {

        $pad2 = strpos($sql, ":", $pad1+1);
        $pad3 = strpos($sql, "}", $pad1+2);

        $search = substr($sql, $pad1,  ($pad3-$pad1)+1);
        $length = substr($sql, $pad2+1,($pad3-$pad2)-1);

        if ( strlen($replace) > $length )
          $replace = substr($replace, 0, $length);

        $sql = str_replace($search, mysqli_real_escape_string($padSqlConnect, $replace), $sql);

      }

    }

    $split   = explode(' ', trim($sql), 2);
    $command = trim(strtolower($split[0]));

    if ($command == 'select')
      $command = 'array';

    if ( $command == 'record' )
      $padDataSetRecord = TRUE;

    if     ( $command == 'check'  )  $sql = 'select 1 from ' . $split[1] . ' limit 0,1';
    elseif ( $command == 'record' )  $sql = 'select '        . $split[1] . ' limit 0,1';
    elseif ( $command == 'field'  )  $sql = 'select '        . $split[1] . ' limit 0,1';
    elseif ( $command == 'array'  )  $sql = 'select '        . $split[1];

    $_SQL [] = $sql;

    $query = mysqli_query ( $padSqlConnect , $sql );

    if ( ! $query )
      padError ( 'SQL: ' . mysqli_errno ( $padSqlConnect ) . ': ' . mysqli_error ( $padSqlConnect ) . ' / '. $sql );

    $padDbRowsFound = $rows = mysqli_affected_rows($padSqlConnect);

    if ( $rows > 0 and ($command == 'field' or $command == 'record') )
      $fields = mysqli_fetch_assoc ( $query );

    if     ( $command == 'insert'  ) {
      $return = mysqli_insert_id ( $padSqlConnect );
      if ( !$return )
        $return = $rows;
    }
    elseif ( $command == 'set')       $return = $rows;
    elseif ( $command == 'truncate')  $return = $rows;
    elseif ( $command == 'load'    )  $return = $rows;
    elseif ( $command == 'replace' )  $return = $rows;
    elseif ( $command == 'update'  )  $return = $rows;
    elseif ( $command == 'delete'  )  $return = $rows;
    elseif ( $command == 'check'   )  $return = $rows;
    elseif ( $command == 'field'   )
      if ( $rows < 1 )
        $return = '';
      else
        foreach ($fields as $key => $return)
          break;
    elseif ( $command == 'record'  )
      if ( $rows < 1 )
        $return = array();
      else
        $return = $fields;
    elseif ( $command == 'array'  ) {
      $return = array();
      if ( $rows > 0 )
        for ( $i = 1; $record = mysqli_fetch_assoc ($query); $i ++ )
          if ( isset($record['id']) and !isset($return [$record['id']]) )
            $return [$record['id']] = $record;
          else
            $return [] = $record;
    }
    else
      $return = '';

    if ( $padInfo )
      include PAD . 'events/sql.php';

    return $return;

  }

?>