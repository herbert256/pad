<?php


  function pad_xxx_to_array ($xxx) {

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    $error_level = error_reporting(0);

    $array = [];

    try {
      $array = (array) $xxx;
    }
    catch (Throwable $e) {
      $array = [];
    }
    
    if     ( $array === NULL     )  $array = [];
    elseif ( ! is_array ($array) )  $array = [];

    error_reporting($error_level);
    restore_error_handler();
    
    return $array;
    
  }


  function pad_track_vars ($file, $info='') {

    $GLOBALS ['pad_track_vars_file'] = $file ;   
    
    pad_dump ($info);
      
    unset ( $GLOBALS ['pad_track_vars_file'] ) ;   

  }
  

  function pad_exit () {

    $GLOBALS['pad_exit'] = 9;
    $GLOBALS['pad_no_boot_shutdown'] = TRUE;
    
    exit();

  }


  function pad_header ($header) {

    if ( headers_sent () )
      return;

    $GLOBALS['pad_headers'] [] = $header;
 
    header ($header);
  
  }


  function pad_field_name ($parm) {
    
    return (substr($parm, 0, 1) == '$') ? substr($parm, 1) : $parm;

  }


  function pad_get_vars () {

    global $pad_session_vars;
    
    pad_get_parms ('POST',   $_POST  );
    pad_get_parms ('GET',    $_GET   );
    pad_get_parms ('COOKIE', $_COOKIE);
  
    if (count($pad_session_vars) ) {
  
      if ( ! ini_get('session.auto_start') )
        session_start();
  
      pad_get_parms ('SESSION', $_SESSION);
      
      foreach ($pad_session_vars as $pad_var)
        if ( ! isset ($GLOBALS [$pad_var]) )
          $GLOBALS [$pad_var] = '';
  
      $GLOBALS['pad_session_started'] = TRUE;
        
    }
  
  }


  function pad_track_output () {

    global $pad_etag, $pad_output;
    
    $pad_content_store_file = "output/$pad_etag.html";

    if ( ! file_exists(PAD_DATA . "$pad_content_store_file") )
      pad_file_put_contents ($pad_content_store_file, $pad_output);

  }


  function pad_id () {

    return $GLOBALS['PADREQID'] ?? uniqid(TRUE);

  }
  

  function pad_check_value (&$value) {

    if     ($value === NULL)      $value = '';
    elseif ($value === TRUE)      $value = '1';
    elseif ($value === FALSE)     $value = '';
    elseif (is_array($value))     $value = '';
    elseif (is_object($value))    $value = '';
    elseif (is_resource($value))  $value = '';
    else                          $value = (string) $value;
    
  }


  function pad_fatal ($error) {
    
    pad_error ($error);
    
    pad_exit ();
    
  }


  function pad_check_page () {

    global $app, $page;
  
    $pad_location = PAD_APP . "pages";

    $pad_page_parts = pad_split ($page, '/');
    
    $file = '';
    
    foreach ($pad_page_parts as $key => $value) {
      
      if ($value == 'inits')
        return pad_fatal ("'inits' is a reserved word and can not be used as page name");

      if ($value == 'exits')
        return pad_fatal ("'exits' is a reserved word and can not be used as page name");

      if ( $key == array_key_last($pad_page_parts)
            and (file_exists("$pad_location/$value.php") or file_exists("$pad_location/$value.html") ) )
      
        return; 
       
      elseif ( is_dir ("$pad_location/$value") ) {

        $file = 'index';
        $pad_location .= "/$value";

      } else {

        return pad_fatal ("Page '$app/$page' not found (1): $pad_location/$value.php");

      }
      
    }
    
    if ( ! file_exists("$pad_location/$file.php") and ! file_exists("$pad_location/$file.html") )
      return pad_error ("Page '$app/$page' not found (2)");

    $page = str_replace(PAD_APP . "pages/", '', "$pad_location/$file");
    
  }

  
  function pad_close_html () {

    echo "\r\n";
    for ($i = 1; $i <= 25; $i++)
      echo "</pre></div></td></tr></th></table></font></span></blockquote></h1></h2></h3></h4></h5></h6></b></i></u></p></ul></li></ol></dl></dt></dd>\r\n";

  }


  function pad_local () {

    if ( !  isset($GLOBALS['pad_local']) )
      return FALSE;
    
    $host = strtolower(trim($_SERVER['HTTP_HOST']??''));
    $ip   = $_SERVER ['REMOTE_ADDR'] ?? '';
    $name = $_SERVER ['SERVER_NAME'] ?? '';

    if ( in_array($host, $GLOBALS['pad_local']) )  return TRUE;
    if ( in_array($ip,   $GLOBALS['pad_local']) )  return TRUE;
    if ( in_array($name, $GLOBALS['pad_local']) )  return TRUE;
    return FALSE;
    
  }


  function pad_explode ( $haystack, $limit, $number=0 ) {

    if ($number)
      $explode = explode ( $limit, $haystack, $number );
    else
      $explode = explode ( $limit, $haystack );
    
    foreach  ($explode as $key => $value ) {

      if ( $limit == '|' ) $explode [$key] = str_replace ( '&pipe;',  '|', $explode [$key] );
      if ( $limit == '=' ) $explode [$key] = str_replace ( '&eq;',    '=', $explode [$key] );
      if ( $limit == ',' ) $explode [$key] = str_replace ( '&comma;', ',', $explode [$key] );

      $explode [$key] = (string) trim ( $value );

      if ( $explode[$key] === '' )
        unset ( $explode[$key] );

    }

    return array_values ( $explode );
    
  }


  function pad_split ($haystack, $split) {

    $explode = explode($split, $haystack);
    
    foreach($explode as $key => $value) {
      if ( trim($explode [$key]) == '' )
        unset($explode[$key]);
    }

    return $explode;
    
  }
  
  function pad_short_md5 ($input) {
    return substr(pad_base64(md5($input,TRUE)),0,22);
  }

  function pad_md5_bin ($short) {
    return base64_decode(strtr($short,'_-','+/').'==');
  }
  
  function pad_short_md5_to_long ($short) {
    return unpack('H*',base64_decode(strtr($short,'_-','+/').'=='))[1];
  }

  function pad_random_string ($len) {
    return substr(strtr(base64_encode(random_bytes(ceil(($len/4)*3))),'+/','_-'),0,$len);
  }

  function pad_base64 ($string) {
    return strtr(base64_encode($string),'+/','_-');
  }

  function pad_file_put_contents ($file, $data, $append=0) {
    
    if ( ! preg_match('/^[A-Za-z0-9_\-\/\.]+$/', $file) or strpos($file, '//') or strpos($file, '..') )
      return pad_error ("Invalid file name: $file");

    $file = PAD_DATA . $file;

    pad_check_file ($file);
    
    pad_timing_start ('write');
    if ($append) file_put_contents ($file, $data, LOCK_EX | FILE_APPEND);
    else         file_put_contents ($file, $data, LOCK_EX);
    pad_timing_end ('write');
    
  }

  function pad_valid_name ($name) {
 
    if ( ! preg_match('/^[A-Za-z0-9_:#]+$/', $name ) ) return FALSE;
    if ( ! ctype_alpha(substr($name, 0, 1))         )  return FALSE;

    return TRUE;

  }


  function pad_unescape ( $string ) {
    return str_replace ( ['&open;','&close;','&pipe;', '&eq;'], ['{','}','|','='], $string );
  }
  function pad_escape ( $string ) {
    return str_replace ( ['{','}','|', '='], ['&open;','&close;','&pipe;', '&eq;'],  $string );
  }


  function pad_html ($html) {

    global $pad_html, $pad_start, $pad_end, $pad_lvl;

    $pad_html[$pad_lvl] = substr($pad_html[$pad_lvl], 0, $pad_start[$pad_lvl])
                        . $html
                        . substr($pad_html[$pad_lvl], $pad_end[$pad_lvl]+1);
    
  }
  
  
  function pad_check_file ($file) {

    $pos = strrpos($file, '/');
    $dir = substr($file, 0, $pos);
    
    if (!file_exists($dir))
      mkdir($dir, $GLOBALS['pad_dir_mode'], true);
    
    if (!file_exists($file)) {
      touch($file);
      chmod($file, $GLOBALS['pad_file_mode']);
    }
  
  }


  function pad_zip ($data) {

    return gzencode($data);

  }


  function pad_unzip ($data) {

    return gzdecode($data);

  }
  
  
  function pad_duration ( $start, $end=0 ) {

    if ($end)
      $duration = (int) ( ( $end            - $start ) * 1000000 );
    else
      $duration = (int) ( ( microtime(true) - $start ) * 1000000 );

    if (!$duration)
      $duration = 1;

    return $duration;

  }


  function pad_between ($content, $start, $end) {

    $p1 = strpos($content, $start);
    
    if ( $p1 !== FALSE ) {
      $p1 += strlen($start);
      $p2 = strpos($content, $end, $p1);
        if ( $p2 !== FALSE)
          return substr ($content, $p1, $p2-$p1);
    }
    
    return "";
    
  }


  function pad_timing_start ($timing) {
    
    if ( ! $GLOBALS['pad_timing'] )
      return;
    
    global $pad_timings, $pad_timings_start;
    
    if ( ! isset ( $pad_timings[$timing] ) )
      $pad_timings [$timing] = 0;

    $pad_timings_start [$timing] = microtime(true);

  }

  function pad_timing_end ($timing) {
    
    if ( ! $GLOBALS['pad_timing'] )
      return;

    global $pad_timings, $pad_timings_start;

    if ($timing == 'sql' and isset($pad_timings_start ['cache']) and $pad_timings_start ['cache'])
      return;

    $pad_timings [$timing] = $pad_timings [$timing] + (microtime(true) - $pad_timings_start [$timing]) ;
    
    $pad_timings_start [$timing] = 0;
    
  }
  
  function pad_timing_close () {

    if ( ! $GLOBALS['pad_timing'] )
      return;

    global $pad_timings;
    $pad_timings ['init'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];

    foreach ($pad_timings as $key => $val)
      $pad_timings [$key] = (int) ( $val * 1000000 );

    pad_header ('X-PAD-Timings: ' . json_encode($pad_timings) );

  }
  


  function pad_track_db_session ($pad_stop) {

    $session = pad_db( "field id from track_session where sessionid='{1}'", [ 1 => $GLOBALS['PADSESSID'] ] );

    if ( ! $session )
      $session = pad_db ( "insert into track_session values(NULL, '{1}', NOW(), NOW(), 1)", [ 1 => $GLOBALS['PADSESSID'] ] );
    else
      pad_db ( "update track_session set requests=requests+1 where id=$session");
   
    if ( ! $GLOBALS['pad_track_db_request'] )
      return;

    pad_db ( "insert into track_request
              values(NULL, {1}, '{2:32}', '{3:32}', NOW(), '{4}', {5}, '{6}', '{7:32}', '{8:32}', '{9:1023}', '{10:1023}', '{11:1023}', '{12:1023}', '{13:1023}', '{14:25}')",
      [  1 => $session,
         2 => $GLOBALS['app']  ?? '',
         3 => $GLOBALS['page'] ?? '',
         4 => pad_duration($_SERVER['REQUEST_TIME_FLOAT'] ?? 0),
         5 => $GLOBALS['pad_len'] ?? 0,
         6 => $pad_stop ?? '',
         8 => $GLOBALS['pad_etag'] ?? $GLOBALS['pad_cache_etag'] ?? '',
         9 => $_SERVER ['REQUEST_URI']     ?? '' ,
        10 => $_SERVER ['HTTP_REFERER']    ?? '' ,
        11 => $_SERVER ['REMOTE_ADDR']     ?? '' ,
        12 => $_SERVER ['HTTP_USER_AGENT'] ?? '' ,
        13 => $_SERVER ['HTTP_COOKIE']     ?? '' ,
        14 => pad_id ()
      ]
    );
      
  }

  function pad_track_file ($pad_stop) {
    
    $id = pad_id ();
  
    $track = [
        'session'   => $GLOBALS ['PADSESSID'] ?? '',
        'request'   => $GLOBALS ['PADREQID'] ?? '',
        'reference' => $GLOBALS ['PADREFID '] ?? '',
        'app'       => $GLOBALS ['app'] ?? '',
        'page'      => $GLOBALS ['page'] ?? '',
        'start'     => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0,
        'end'       => microtime(true),
        'length'    => $GLOBALS ['pad_len'] ?? 0,
        'stop'      => $pad_stop ?? '',
        'etag'      => $GLOBALS ['pad_etag'] ?? '',
        'uri'       => $_SERVER ['REQUEST_URI']     ?? '' ,
        'referer'   => $_SERVER ['HTTP_REFERER']    ?? '' ,
        'remote'    => $_SERVER ['REMOTE_ADDR']     ?? '' ,
        'agent'     => $_SERVER ['HTTP_USER_AGENT'] ?? '',
        'cookies  ' => $_SERVER ['HTTP_COOKIE']     ?? ''
      ];

    $json = json_encode($track, JSON_PARTIAL_OUTPUT_ON_ERROR);
    
    pad_file_put_contents ( "track/$id.json", $json, 1);
      
  }


  function pad_close_session () {

    if ( ! isset($GLOBALS['pad_session_started']) )
      return;

    foreach ( $GLOBALS ['pad_session_vars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }
  
?>