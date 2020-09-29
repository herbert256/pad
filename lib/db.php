<?php

  function db ( $sql, $vars = [] ) {

    global $pad_sql_connect, $pad_sql_host , $pad_sql_user , $pad_sql_password , $pad_sql_database;
    
    if ( ! isset ( $pad_sql_connect ) ) {
      pad_timing_start ('sql');
      $pad_sql_connect = pad_db_connect ( $pad_sql_host , $pad_sql_user , $pad_sql_password , $pad_sql_database );
      pad_timing_end ('sql');
    }
    
    return pad_db_part2 ($pad_sql_connect, $sql, $vars, 'app');
    
  }
  
  
  function pad_db ( $sql, $vars = [] ) {

    global $pad_pad_sql_connect, $pad_pad_sql_host , $pad_pad_sql_user , $pad_pad_sql_password , $pad_pad_sql_database;
    
    if ( ! isset ( $pad_pad_sql_connect ) ) {
      pad_timing_start ('sql');
      $pad_pad_sql_connect = pad_db_connect ( $pad_pad_sql_host , $pad_pad_sql_user , $pad_pad_sql_password , $pad_pad_sql_database );
      pad_timing_end ('sql');
    }
    
    return pad_db_part2 ($pad_pad_sql_connect, $sql, $vars, 'pad');
    
  }

  
  function pad_db_part2 ( $pad_sql_connect, $sql, $vars, $db_type) {

    global $pad_db_tables, $pad_db_rows_found, $pad_track_sql, $pad_parms_tag;
    
    if ( isset ( $pad_db_tables[$sql] ) ) {
      $pad_parms_tag_save = $pad_parms_tag;
      $pad_parms_tag = [];
      $result = pad_db_get_data ($sql); 
      $pad_parms_tag = $pad_parms_tag_save;
      return $result;
    }

    foreach ( $vars as $i => $replace ) {

      $p1 = strpos($sql, '{'.$i.'}' );

      if ( $p1 !== FALSE )
        if (substr($i, 0, 1) <> 'x')
          $sql = str_replace('{'.$i.'}', mysqli_real_escape_string($pad_sql_connect, $replace), $sql);
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

        $sql = str_replace($search, mysqli_real_escape_string($pad_sql_connect, $replace), $sql);

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

    if ( $pad_track_sql )
      $pad_sql_start = microtime(true);
    
    pad_timing_start ('sql');
    $query = mysqli_query ( $pad_sql_connect , $sql );
    pad_timing_end ('sql');

    $GLOBALS['pad_last_sql'] = $sql;
    
    if ( ! $query )
      padError ( 'MySQL-' . mysqli_errno ( $pad_sql_connect ) . ': ' . mysqli_error ( $pad_sql_connect )  . '<br><br>' . $sql );

    pad_timing_start ('sql');
    $pad_db_rows_found = $rows = mysqli_affected_rows($pad_sql_connect);
    pad_timing_end ('sql');

    if ( $rows > 0 and ($command == 'field' or $command == 'record') ) {
      pad_timing_start ('sql');
      $fields = mysqli_fetch_assoc ( $query );
      $GLOBALS['pad_db_last_fields'] = $fields;
      pad_timing_end ('sql');
    }

    if     ( $command == 'insert'  ) {
      pad_timing_start ('sql');
      $return = mysqli_insert_id ( $pad_sql_connect );
      pad_timing_end ('sql');
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


    if ( $GLOBALS['pad_track_sql'] ) {

      $pad_sql_duration = pad_duration ($pad_sql_start);

      if ($GLOBALS['pad_track_sql'])
        pad_db_log ($db_type, $pad_sql_duration, $pad_db_rows_found, pad_db_format_sql($sql)) ;
      
    }

    return $return;

  }


  function pad_db_log ($type, $duration, $rows, $sql) {
        
    $backTrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS,3);
    extract ( $backTrace[2] );

    $now   = microtime(true);
    $sec   = floor($now);
    $micro = str_pad($now - $sec, 6, '0', STR_PAD_LEFT);
    $start = date('Y-m-d H:i:s', $sec) . '.' . $micro . ' ' . pad_id ();

    $log = "$start $file:$line rows:$rows time:$duration"
         . "\r\n\r\n$sql\r\n----------------------------------------\r\n";

    pad_file_put_contents ("sql_$type.txt", $log, 1);
    
  }


  function pad_db_connect ( $host, $user, $password, $database ) {

    $connect = mysqli_connect ( "p:$host" , $user , $password , $database );
    
    if ( ! $connect )
      pad_fatal ( mysqli_connect_errno ( ) . ' - ' . mysqli_connect_error ( ) );
      
    mysqli_query($connect, "SET SESSION sql_mode = 'TRADITIONAL'");
    
    return $connect;
    
  }

  function pad_db_format_sql ($sql) {

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


  function pad_db_escape ($inp) {

    if (is_array($inp))
        return array_map (__METHOD__, $inp);

    if (!empty($inp) && is_string($inp)) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
    }

    return $inp;

  }

?>