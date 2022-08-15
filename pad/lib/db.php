<?php

  function db ( $sql, $vars = [] ) {

    global $padSql_connect, $padSql_host , $padSql_user , $padSql_password , $padSql_database;
    
    if ( ! isset ( $padSql_connect ) ) {
      pTiming_start ('sql');
      $padSql_connect = pDb_connect ( $padSql_host , $padSql_user , $padSql_password , $padSql_database );
      pTiming_end ('sql');
    }
    
    return pDb_part2 ($padSql_connect, $sql, $vars, 'app');
    
  }
  
  
  function pDb ( $sql, $vars = [] ) {

    global $padPad_sql_connect, $padPad_sql_host , $padPad_sql_user , $padPad_sql_password , $padPad_sql_database;
    
    if ( ! isset ( $padPad_sql_connect ) ) {
      pTiming_start ('sql');
      $padPad_sql_connect = pDb_connect ( $padPad_sql_host , $padPad_sql_user , $padPad_sql_password , $padPad_sql_database );
      pTiming_end ('sql');
    }
    
    return pDb_part2 ($padPad_sql_connect, $sql, $vars, 'pad');
    
  }

  
  function pDb_part2 ( $padSql_connect, $sql, $vars, $db_type ) {

    global $pad, $padDb_rows_found, $padTrack_sql, $padPrmsTag;
    
    foreach ( $vars as $i => $replace ) {

      $pad1 = strpos($sql, '{'.$i.'}' );

      if ( $pad1 !== FALSE )
        if (substr($i, 0, 1) <> 'x')
          $sql = str_replace('{'.$i.'}', mysqli_real_escape_string($padSql_connect, $replace), $sql);
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

        $sql = str_replace($search, mysqli_real_escape_string($padSql_connect, $replace), $sql);

      }

    }

    $split   = explode(' ', trim($sql), 2);
    $command = trim(strtolower($split[0]));

    if ($command == 'select')
      $command = 'array';

    if     ( $command == 'check'  )  $sql = 'select 1 from ' . $split[1] . ' limit 0,1';
    elseif ( $command == 'record' )  $sql = 'select '        . $split[1] . ' limit 0,1';
    elseif ( $command == 'field'  )  $sql = 'select '        . $split[1] . ' limit 0,1';
    elseif ( $command == 'array'  )  $sql = 'select '        . $split[1];

    if ( $padTrack_sql )
      $padSql_start = microtime(true);
    
    pTiming_start ('sql');
    $query = mysqli_query ( $padSql_connect , $sql );
    pTiming_end ('sql');

    $GLOBALS ['padLast_sql'] = $sql;
    
    if ( ! $query )
      pError ( 'MySQL-' . mysqli_errno ( $padSql_connect ) . ': ' . mysqli_error ( $padSql_connect ) . ' - '. $sql );

    pTiming_start ('sql');
    $padDb_rows_found = $rows = mysqli_affected_rows($padSql_connect);
    pTiming_end ('sql');

    if ( $rows > 0 and ($command == 'field' or $command == 'record') ) {
      pTiming_start ('sql');
      $fields = mysqli_fetch_assoc ( $query );
      $GLOBALS ['padDb_last_fields'] = $fields;
      pTiming_end ('sql');
    }

    if     ( $command == 'insert'  ) {
      pTiming_start ('sql');
      $return = mysqli_insert_id ( $padSql_connect );
      pTiming_end ('sql');
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


    if ( $GLOBALS ['padTrack_sql'] ) {

      $padSql_duration = pDuration ($padSql_start);

      if ($GLOBALS ['padTrack_sql'])
        pDb_log ($db_type, $padSql_duration, $padDb_rows_found, pDb_format_sql($sql)) ;
      
    }

    return $return;

  }


  function pDb_log ($type, $duration, $rows, $sql) {
        
    $backTrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS,3);
    extract ( $backTrace[2] );

    $now   = microtime(true);
    $sec   = floor($now);
    $micro = (int) (($now - $sec) * 1000);
    $micro = str_pad($micro, 3, '0', STR_PAD_LEFT);

    $start = date('Y-m-d H:i:s', $sec) . '.' . $micro . ' ' . $GLOBALS ['PADREQID'];

    $log = "$start $file:$line rows:$rows time:$duration"
         . "\r\n\r\n$sql\r\n----------------------------------------\r\n";

    pFile_put_contents ("sql/$type.txt", $log, 1);
    
  }


  function pDb_connect ( $host, $user, $padassword, $database ) {

    $connect = mysqli_connect ( "p:$host" , $user , $padassword , $database );
    
    if ( ! $connect )
      return pError ( mysqli_connect_errno ( ) . ' - ' . mysqli_connect_error ( ) );
      
    mysqli_query($connect, "SET SESSION sql_mode = 'TRADITIONAL'");
    
    return $connect;
    
  }

  function pDb_format_sql ($sql) {

    $work = preg_replace('/\s+/', ' ', $sql);
    $work = trim($work);

    $work = str_replace('select ',    "select    ", $work);
    $work = str_replace('check ',     "select    1\r\nfrom      ", $work);
    $work = str_replace('record ',    "select    ", $work);
    $work = str_replace('field ',     "select    ", $work);
    $work = str_replace('array ',     "select    ", $work);
    $work = str_replace('insert ',    "insert    ", $work);
    $work = str_replace('delete ',    "delete    ", $work);
    $work = str_replace('update ',    "update    ", $work);
    $work = str_replace(' from ',     "\r\nfrom      ", $work);
    $work = str_replace(' limit ',    "\r\nlimit     ", $work);
    $work = str_replace(' where ',    "\r\nwhere     ", $work);
    $work = str_replace(' and ',      "\r\n  and     ", $work);
    $work = str_replace(' group by ', "\r\ngroup by  ", $work);
    $work = str_replace(' order by ', "\r\norder by  ", $work);

    return $work;

  }


  function pDb_escape ($inp) {

    if (is_array($inp))
        return array_map (__METHOD__, $inp);

    if (!empty($inp) && is_string($inp)) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
    }

    return $inp;

  }

?>