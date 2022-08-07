<?php

  function db ( $sql, $vars = [] ) {

    global $pSql_connect, $pSql_host , $pSql_user , $pSql_password , $pSql_database;
    
    if ( ! isset ( $pSql_connect ) ) {
      pTiming_start ('sql');
      $pSql_connect = pDb_connect ( $pSql_host , $pSql_user , $pSql_password , $pSql_database );
      pTiming_end ('sql');
    }
    
    return pDb_part2 ($pSql_connect, $sql, $vars, 'app');
    
  }
  
  
  function pDb ( $sql, $vars = [] ) {

    global $pPad_sql_connect, $pPad_sql_host , $pPad_sql_user , $pPad_sql_password , $pPad_sql_database;
    
    if ( ! isset ( $pPad_sql_connect ) ) {
      pTiming_start ('sql');
      $pPad_sql_connect = pDb_connect ( $pPad_sql_host , $pPad_sql_user , $pPad_sql_password , $pPad_sql_database );
      pTiming_end ('sql');
    }
    
    return pDb_part2 ($pPad_sql_connect, $sql, $vars, 'pad');
    
  }

  
  function pDb_part2 ( $pSql_connect, $sql, $vars, $db_type) {

    global $pDb_tables, $pDb_rows_found, $pTrack_sql, $pPrms_tag;
    
    if ( isset ( $pDb_tables[$sql] ) ) {
      $pPrms_tag_save = $pPrms_tag;
      $pPrms_tag = [];
      $result = pDb_get_data ($sql); 
      $pPrms_tag = $pPrms_tag_save;
      return $result;
    }

    foreach ( $vars as $i => $replace ) {

      $p1 = strpos($sql, '{'.$i.'}' );

      if ( $p1 !== FALSE )
        if (substr($i, 0, 1) <> 'x')
          $sql = str_replace('{'.$i.'}', mysqli_real_escape_string($pSql_connect, $replace), $sql);
        else
          $sql = str_replace('{'.$i.'}', $replace, $sql);

      $p1 = strpos($sql, '{'.$i.':' );

      if ($p1 !== FALSE) {

        $p2 = strpos($sql, ":", $p1+1);
        $p3 = strpos($sql, "}", $p1+2);

        $search = substr($sql, $p1,  ($p3-$p1)+1);
        $length = substr($sql, $p2+1,($p3-$p2)-1);

        if ( strlen($replace) > $length )
          $replace = substr($replace, 0, $length);

        $sql = str_replace($search, mysqli_real_escape_string($pSql_connect, $replace), $sql);

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

    if ( $pTrack_sql )
      $pSql_start = microtime(true);
    
    pTiming_start ('sql');
    $query = mysqli_query ( $pSql_connect , $sql );
    pTiming_end ('sql');

    $GLOBALS['pLast_sql'] = $sql;
    
    if ( ! $query )
      pError ( 'MySQL-' . mysqli_errno ( $pSql_connect ) . ': ' . mysqli_error ( $pSql_connect ) . ' - '. $sql );

    pTiming_start ('sql');
    $pDb_rows_found = $rows = mysqli_affected_rows($pSql_connect);
    pTiming_end ('sql');

    if ( $rows > 0 and ($command == 'field' or $command == 'record') ) {
      pTiming_start ('sql');
      $fields = mysqli_fetch_assoc ( $query );
      $GLOBALS['pDb_last_fields'] = $fields;
      pTiming_end ('sql');
    }

    if     ( $command == 'insert'  ) {
      pTiming_start ('sql');
      $return = mysqli_insert_id ( $pSql_connect );
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


    if ( $GLOBALS['pTrack_sql'] ) {

      $pSql_duration = pDuration ($pSql_start);

      if ($GLOBALS['pTrack_sql'])
        pDb_log ($db_type, $pSql_duration, $pDb_rows_found, pDb_format_sql($sql)) ;
      
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

    $start = date('Y-m-d H:i:s', $sec) . '.' . $micro . ' ' . $GLOBALS['PADREQID'];

    $log = "$start $file:$line rows:$rows time:$duration"
         . "\r\n\r\n$sql\r\n----------------------------------------\r\n";

    pFile_put_contents ("sql/$type.txt", $log, 1);
    
  }


  function pDb_connect ( $host, $user, $password, $database ) {

    $connect = mysqli_connect ( "p:$host" , $user , $password , $database );
    
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