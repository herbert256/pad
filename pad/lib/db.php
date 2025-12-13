<?php


  /**
   * Executes a SQL query against the application database.
   *
   * Automatically connects on first call using configured credentials.
   * Supports placeholder substitution and various result types based on
   * the SQL command prefix (select, field, record, check, etc.).
   *
   * @param string $sql  The SQL query with {0}, {1}, etc. placeholders.
   * @param array  $vars Values to substitute into placeholders.
   *
   * @return mixed Query result (array, string, int, or FALSE on error).
   *
   * @global resource $padSqlConnect  Application database connection.
   * @global string   $padSqlHost     Database host.
   * @global string   $padSqlUser     Database username.
   * @global string   $padSqlPassword Database password.
   * @global string   $padSqlDatabase Database name.
   */
  function db ( $sql, $vars = [] ) {

    if ( ! function_exists ( 'mysqli_connect' ) )
      return FALSE;

    global $padSqlConnect, $padSqlHost, $padSqlUser, $padSqlPassword, $padSqlDatabase;
    
    if ( ! isset ( $padSqlConnect ) )
      $padSqlConnect = padDbConnect ( $padSqlHost, $padSqlUser, $padSqlPassword, $padSqlDatabase );

    return padDbPart2 ( $padSqlConnect, $sql, $vars );
    
  }

  
  /**
   * Executes a SQL query against the PAD framework database.
   *
   * Similar to db() but uses the PAD-specific database connection
   * for framework internal data (sessions, cache, etc.).
   *
   * @param string $sql  The SQL query with {0}, {1}, etc. placeholders.
   * @param array  $vars Values to substitute into placeholders.
   *
   * @return mixed Query result (array, string, int, or FALSE on error).
   *
   * @global resource $padSqlPadConnect  PAD database connection.
   * @global string   $padSqlPadHost     PAD database host.
   * @global string   $padSqlPadUser     PAD database username.
   * @global string   $padSqlPadPassword PAD database password.
   * @global string   $padSqlPadDatabase PAD database name.
   */
  function padDb ( $sql, $vars = [] ) {

    if ( ! function_exists ( 'mysqli_connect' ) )
      return FALSE;

    global $padSqlPadConnect, $padSqlPadHost , $padSqlPadUser , $padSqlPadPassword , $padSqlPadDatabase;

    if ( ! isset ( $padSqlPadConnect ) )
      $padSqlPadConnect = padDbConnect ( $padSqlPadHost , $padSqlPadUser , $padSqlPadPassword , $padSqlPadDatabase );

    return padDbPart2 ($padSqlPadConnect, $sql, $vars);

  }

 
  /**
   * Establishes a MySQL database connection.
   *
   * Creates a mysqli connection and sets TRADITIONAL SQL mode.
   *
   * @param string $host     Database server hostname.
   * @param string $user     Database username.
   * @param string $password Database password.
   * @param string $database Database name.
   *
   * @return mysqli|false The connection handle, or FALSE on error.
   */
  function padDbConnect ( $host, $user, $password, $database ) {

    $connect = mysqli_connect ( "$host" , $user , $password , $database );

    if ( ! $connect )
      return padError ( mysqli_connect_errno ( ) . ' - ' . mysqli_connect_error ( ) );

    mysqli_query($connect, "SET SESSION sql_mode = 'TRADITIONAL'");

    return $connect;

  }


  /**
   * Core SQL query processor - handles placeholder substitution and execution.
   *
   * Processes SQL with placeholder substitution, executes the query, and returns
   * results based on the command prefix:
   * - 'field': Returns single value from first row
   * - 'record': Returns first row as array
   * - 'array'/'select': Returns all rows as array (keyed by 'id' if present)
   * - 'check': Returns row count (used to check existence)
   * - insert/update/delete: Returns affected rows or insert ID
   *
   * Placeholder formats: {0}, {1} for direct substitution; {1:32} to truncate to 32 chars.
   *
   * @param mysqli $padSqlConnect The database connection.
   * @param string $sql           The SQL query with placeholders.
   * @param array  $vars          Values to substitute into placeholders.
   *
   * @return mixed Query result based on command type.
   *
   * @global int $padDbRowsFound Number of rows affected/returned.
   */
  function padDbPart2 ( $padSqlConnect, $sql, $vars ) {

    global $pad, $padDbRowsFound, $padPrm;

    $input = $sql;
    
    foreach ( $vars as $i => $replace ) {

      $pad1 = strpos($sql, '{'.$i.'}' );

      if ( $pad1 !== FALSE )
        if (substr($i, 0, 1) <> 'x')
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
      $GLOBALS ['padDataSetRecord'] = TRUE;

    if     ( $command == 'check'  )  $sql = 'select 1 from ' . $split[1] . ' limit 0,1';
    elseif ( $command == 'record' )  $sql = 'select '        . $split[1] . ' limit 0,1';
    elseif ( $command == 'field'  )  $sql = 'select '        . $split[1] . ' limit 0,1';
    elseif ( $command == 'array'  )  $sql = 'select '        . $split[1];

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

    if ( $GLOBALS ['padInfo'] )
      include PAD . 'events/sql.php';

    return $return;

  }


?>