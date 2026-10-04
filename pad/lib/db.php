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
  //   - {0}, {1} ... placeholders are replaced from $vars by padDbPlaceholders: escaped
  //     inside a quoted literal, a number or a quoted literal when written bare, raw when
  //     the key starts with x; {0:20} truncates to that length first
  //   - the leading verb decides the shape of the result: CHECK (rowcount, note the
  //     "CHECK table WHERE ..." form), FIELD (one scalar), RECORD (one assoc row), ARRAY
  //     or SELECT (rows, keyed by an id column when the query returns one), INSERT (the
  //     new id, else the row count), UPDATE/DELETE/REPLACE/SET/TRUNCATE/LOAD (row count)
  //   - every statement is appended to $_SQL; failures go through padError, and
  //     events/sql.php logs the query when info is on

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

    global $_SQL, $pad, $padDataSetRecord, $padInfo, $padPrm;

    $input = $sql;

    if ( count ( $vars ) )
      $sql = padDbPlaceholders ( $padSqlConnect, $sql, $vars );

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

    $rows = mysqli_affected_rows($padSqlConnect);

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

  // Fills the {0}, {1} ... placeholders in one pass over the statement, so a value that
  // itself holds {1} is never substituted again by the next placeholder. A placeholder the
  // statement writes inside a quoted literal - name = '{0}' - gets the value escaped, as it
  // always did. One written bare - id = {0} - gets a number as a number and anything else
  // as a quoted, escaped literal: escaping alone protected nothing there, and the documented
  // id = {0} took '0 or 1=1' as SQL. An array there becomes a list, for IN ({0}). Keys
  // starting x are inserted raw, a deliberate escape hatch; {0:20} cuts the value to that
  // many characters first.

  function padDbPlaceholders ( $connect, $sql, $vars ) {

    $out   = '';
    $len   = strlen ( $sql );
    $quote = '';

    for ( $i = 0; $i < $len; $i++ ) {

      $char = $sql [$i];

      if ( $quote ) {

        if ( $char == '\\' and $i + 1 < $len ) {
          $out .= $char . $sql [++$i];
          continue;
        }

        if ( $char == $quote )
          $quote = '';

      } elseif ( $char == "'" or $char == '"' or $char == '`' )

        $quote = $char;

      if ( $char == '{' and preg_match ( '/\G\{([A-Za-z0-9_]+)(?::(\d+))?\}/', $sql, $match, 0, $i ) ) {

        $key = $match [1];

        if ( ! array_key_exists ( $key, $vars ) ) {
          $out .= $char;
          continue;
        }

        $value = $vars [$key];

        if ( isset ( $match [2] ) and is_scalar ( $value ) )
          $value = mb_substr ( (string) $value, 0, (int) $match [2] );

        if     ( $key [0] == 'x'    ) $out .= is_array ( $value ) ? implode ( ',', $value ) : $value;
        elseif ( $quote == '`'      ) $out .= str_replace ( '`', '``', (string) $value );
        elseif ( $quote             ) $out .= padDbEscape ( $connect, $value );
        else                          $out .= padDbLiteral ( $connect, $value );

        $i += strlen ( $match [0] ) - 1;
        continue;

      }

      $out .= $char;

    }

    return $out;

  }

  // A NULL is an empty string here, as padEscape has it; an array inside quotes is its
  // values joined with commas.

  function padDbEscape ( $connect, $value ) {

    if ( is_array ( $value ) )
      $value = implode ( ',', $value );

    return mysqli_real_escape_string ( $connect, (string) ( $value ?? '' ) );

  }

  function padDbLiteral ( $connect, $value ) {

    if ( is_array ( $value ) )
      return count ( $value )
           ? implode ( ',', array_map ( fn ( $one ) => padDbLiteral ( $connect, $one ), $value ) )
           : 'NULL';

    if ( $value === NULL )
      return 'NULL';

    if ( is_bool ( $value ) )
      return $value ? '1' : '0';

    if ( is_int ( $value ) or is_float ( $value ) or preg_match ( '/^-?\d+(\.\d+)?$/', (string) $value ) )
      return (string) $value;

    return "'" . mysqli_real_escape_string ( $connect, (string) $value ) . "'";

  }

?>