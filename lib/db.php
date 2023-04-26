<?php

  function db ( $sql, $vars = [] ) {

    global $padSqlConnect, $padSqlHost , $padSqlUser , $padSqlPassword , $padSqlDatabase;
    
    if ( ! isset ( $padSqlConnect ) )
      $padSqlConnect = padDbConnect ( $padSqlHost , $padSqlUser , $padSqlPassword , $padSqlDatabase );
    
    return padDbPart2 ($padSqlConnect, $sql, $vars);
    
  }
  
  
  function padDb ( $sql, $vars = [] ) {

    global $padPadSqlConnect, $padPadSqlHost , $padPadSqlUser , $padPadSqlPassword , $padPadSqlDatabase;
    
    if ( ! isset ( $padPadSqlConnect ) )
      $padPadSqlConnect = padDbConnect ( $padPadSqlHost , $padPadSqlUser , $padPadSqlPassword , $padPadSqlDatabase );
    
    return padDbPart2 ($padPadSqlConnect, $sql, $vars);
    
  }

  
  function padDbPart2 ( $padSqlConnect, $sql, $vars ) {

    global $pad, $padDbRowsFound, $padPrm;
    
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

    if     ( $command == 'check'  )  $sql = 'select 1 from ' . $split[1] . ' limit 0,1';
    elseif ( $command == 'record' )  $sql = 'select '        . $split[1] . ' limit 0,1';
    elseif ( $command == 'field'  )  $sql = 'select '        . $split[1] . ' limit 0,1';
    elseif ( $command == 'array'  )  $sql = 'select '        . $split[1];
    
    $query = mysqli_query ( $padSqlConnect , $sql );

    $GLOBALS ['padLastSql'] = padDbFormatSql($sql);
    
    if ( ! $query )
      padError ( 'MySQL-' . mysqli_errno ( $padSqlConnect ) . ': ' . mysqli_error ( $padSqlConnect ) . ' - '. $sql );

    $padDbRowsFound = $rows = mysqli_affected_rows($padSqlConnect);

    if ( $rows > 0 and ($command == 'field' or $command == 'record') ) {
      $fields = mysqli_fetch_assoc ( $query );
      $GLOBALS ['padLastFields'] = $fields;
    }

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

    return $return;

  }

  function padDbConnect ( $host, $user, $padassword, $database ) {

    $connect = mysqli_connect ( "p:$host" , $user , $padassword , $database );
    
    if ( ! $connect )
      return padError ( mysqli_connect_errno ( ) . ' - ' . mysqli_connect_error ( ) );
      
    mysqli_query($connect, "SET SESSION sql_mode = 'TRADITIONAL'");
     
    return $connect;
    
  }

  function padDbFormatSql ($sql) {

    $work = preg_replace('/\s+/', ' ', $sql);
    $work = trim($work);

    $work = str_replace('select ',    "select    ", $work);
    $work = str_replace('insert ',    "insert    ", $work);
    $work = str_replace('delete ',    "delete    ", $work);
    $work = str_replace('update ',    "update    ", $work);

    $work = str_replace(' from ',     "\r\nfrom      ", $work);
    $work = str_replace(' limit ',    "\r\nlimit     ", $work);
    $work = str_replace(' where ',    "\r\nwhere     ", $work);
    $work = str_replace(' and ',      "\r\n  and     ", $work);
    $work = str_replace(' or ',       "\r\n   or     ", $work);
    $work = str_replace(' group by ', "\r\ngroup by  ", $work);
    $work = str_replace(' order by ', "\r\norder by  ", $work);
    $work = str_replace(' union ',    "\r\nunion     ", $work);
    $work = str_replace(' having ',   "\r\nhaving    ", $work);

    $work = str_replace(" join ",                  "\r\njoin ",               $work);
    $work = str_replace(" inner\r\njoin ",         "\r\ninner join ",         $work);
    $work = str_replace(" natural\r\nleft join ",  "\r\nnatural left join ",  $work);
    $work = str_replace(" natural\r\nright join ", "\r\nnatural right join ", $work);
    $work = str_replace(" left\r\njoin ",          "\r\nleft join ",          $work);
    $work = str_replace(" right\r\njoin ",         "\r\nright join ",         $work);
    $work = str_replace(" natural\r\njoin ",       "\r\nnatural join ",       $work);
    $work = str_replace(" cross\r\njoin ",         "\r\ncross join ",         $work);

    return $work;

  }

?>