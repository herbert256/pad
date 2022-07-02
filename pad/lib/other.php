<?php

  function pad_trace_write ( $dir, $file, $data ) {

    $target = DATA . $GLOBALS['pad_trace_dir_base'] ;
    if ($dir)
      $target .= "/$dir";

    if ( ! file_exists($target) )
      mkdir($target, $GLOBALS['pad_dir_mode'], true);

    $target .= "/$file";

    if ( ! file_exists($target) ) {
      touch($target);
      chmod($target, $GLOBALS['pad_file_mode']);
    }

    file_put_contents ( $target, $data, FILE_APPEND | LOCK_EX );

  }


  function pad_local () {

    if ( ! isset($GLOBALS['pad_local']) )
      return FALSE;
    
    $host = strtolower(trim($_SERVER['HTTP_HOST']??''));
    $ip   = $_SERVER ['REMOTE_ADDR'] ?? '';
    $name = $_SERVER ['SERVER_NAME'] ?? '';

    if ( in_array ( $host, $GLOBALS['pad_local'] ) ) return TRUE;
    if ( in_array ( $ip,   $GLOBALS['pad_local'] ) ) return TRUE;
    if ( in_array ( $name, $GLOBALS['pad_local'] ) ) return TRUE;

    return FALSE;
    
  }


  function pad_explode ( $haystack, $limit, $number=0 ) {

    if ($number)
      $explode = explode ( $limit, $haystack, $number );
    else
      $explode = explode ( $limit, $haystack );
    
    foreach  ($explode as $key => $value ) {

      $explode[$key] = trim($value);
    
      if ( $limit == '|' ) $explode [$key] = str_replace ( '&pipe;',  '|', $explode [$key] );
      if ( $limit == '=' ) $explode [$key] = str_replace ( '&eq;',    '=', $explode [$key] );
      if ( $limit == ',' ) $explode [$key] = str_replace ( '&comma;', ',', $explode [$key] );

      if ( $limit == '|' ) $explode [$key] = str_replace ( '#pipe#',  '|', $explode [$key] );
      if ( $limit == '=' ) $explode [$key] = str_replace ( '#eq#',    '=', $explode [$key] );
      if ( $limit == ',' ) $explode [$key] = str_replace ( '#comma#', ',', $explode [$key] );
    
      if ( $explode[$key] === '' )
        unset ( $explode[$key] );

    }

    return array_values ( $explode );
    
  }


  function pad_json ($data) {

    return json_encode ( $data, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_FORCE_OBJECT );

  }


  function pad_empty_buffers () {

    $buffers = ob_get_level ();

    for ($i = 1; $i <= $buffers; $i++)
      $buffer = ob_get_clean();

  }


  function pad_xxx_to_array ($xxx) {

     if ( is_array($xxx) )
       return ($xxx);

    set_error_handler ( function ($s, $m, $f, $l) { $array = []; } );
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
  
      
  function pad_header ($header) {

    if ( headers_sent () )
      return;

    $GLOBALS['pad_headers'] [] = $header;
 
    header ($header);
  
  }


  function pad_field_name ($parm) {
    
    return (substr($parm, 0, 1) == '$') ? substr($parm, 1) : $parm;

  }

  function pad_check_page () {

    global $app, $page;
  
    if ( ! preg_match ( '/^[A-Za-z0-9\/_]+$/', $page ) ) pad_error ("Invalid page name '$page'");
    if ( strpos($page, '//') !== FALSE)                  pad_error ("Invalid page name '$page'");
    if ( substr($page, 0, 1) == '/')                     pad_error ("Invalid page name '$page'");

    $pad_location = APP . "pages";

    $pad_page_parts = pad_split ($page, '/');
    
    $file = '';
    
    foreach ($pad_page_parts as $key => $value) {
      
      if ($value == 'inits')
        return pad_error ("'inits' is a reserved word and can not be used as page name");

      if ($value == 'exits')
        return pad_error ("'exits' is a reserved word and can not be used as page name");

      if ( $key == array_key_last($pad_page_parts)
            and (pad_file_exists("$pad_location/$value.php") or pad_file_exists("$pad_location/$value.html") ) )
      
        return; 
       
      elseif ( is_dir ("$pad_location/$value") ) {

        $file = 'index';
        $pad_location .= "/$value";

      } else {

        return pad_error ("Page '$app/$page' not found (1)");

      }
      
    }
    
    if ( ! pad_file_exists("$pad_location/$file.php") and ! pad_file_exists("$pad_location/$file.html") )
      return pad_error ("Page '$app/$page' not found (2)");

    $page = str_replace(APP . "pages/", '', "$pad_location/$file");
    
  }

  
  function pad_close_html () {

    echo "\r\n";
    for ($i = 1; $i <= 25; $i++)
      echo "</pre></div></td></tr></th></table></font></span></blockquote></h1></h2></h3></h4></h5></h6></b></i></u></p></ul></li></ol></dl></dt></dd>\r\n";

  }


  function pad_split ($haystack, $split) {

    $explode = explode($split, $haystack);
    
    foreach($explode as $key => $value) {
      if ( trim($explode [$key]) == '' )
        unset($explode[$key]);
    }

    return $explode;
    
  }

  function pad_md5 ($input) {
    return substr(pad_base64(pad_pack(md5($input))),0,22);
  }
  
  function pad_md5_unpack ($input) {
    return pad_unpack(pad_unbase64 ($input.'=='));
  }

  function pad_pack ($data) {
    return pack('H*',$data);
  }

  function pad_unpack ($data) {
    return unpack('H*',$data)[1];
  }

  function pad_base64 ($string) {
    return strtr(base64_encode($string),'+/','_-');
  }

  function pad_unbase64 ($string) {
    return base64_decode(strtr($string,'_-','+/'));
  }

  function pad_random_string ($len=16) {
    $random = ceil(($len/4)*3);
    $random = random_bytes($random);
    $random = base64_encode($random);
    $random = substr($random,0,$len);
    $random = str_replace ( '+', pad_random_char(), $random );
    $random = str_replace ( '/', pad_random_char(), $random );
    return $random;
  }

  function pad_random_char () {
    $random = mt_rand(0,61);
    return ($random < 10) ? chr($random+48) : ($random < 36 ? chr($random+55) : chr($random+61));
  }


  function pad_valid ($name) {

    if ( $name == '' ) 
      return FALSE;

    if ( ! preg_match('/^[a-zA-Z_][:#a-zA-Z0-9_]*$/',$name) )
      return FALSE;

    return TRUE;  

  }

  function pad_valid2 ($name) {

    if ( $name === '' ) return FALSE;
 
    if ( ! preg_match('/^[A-Za-z0-9_:#-]+$/', $name ) ) return FALSE;

    if ( substr($name, 0, 1) == '-' or substr($name, 0, 1) == '_' ) return TRUE;

    if ( ! ctype_alpha(substr($name, 0, 1))          ) return FALSE;

    return TRUE;

  }


  function pad_unescape ( $string ) {
    return str_replace ( ['&open;','&close;','&pipe;', '&eq;','&comma;'], ['{','}','|','=',','], $string );
  }
  function pad_escape ( $string ) {
    return str_replace ( ['{','}','|','=',','], ['&open;','&close;','&pipe;', '&eq;', '&comma;'],  $string );
  }


  function pad_html ($html) {

    global $pad_html, $pad_start, $pad_end, $pad_lvl;

    $pad_html[$pad_lvl] = substr($pad_html[$pad_lvl], 0, $pad_start[$pad_lvl])
                        . $html
                        . substr($pad_html[$pad_lvl], $pad_end[$pad_lvl]+1);
    
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


  function pad_close_session () {

    if ( ! isset($GLOBALS['pad_session_started']) )
      return;

    foreach ( $GLOBALS ['pad_session_vars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }
  

  function pad_info ( $input ) {

    if     ( $input === NULL       )  return "NULL";
    elseif ( $input === FALSE      )  return "FALSE";
    elseif ( $input === TRUE       )  return "TRUE";
    elseif ( is_array ( $input)    )  return "ARRAY: "    . pad_make_content ( $input );
    elseif ( is_object ( $input)   )  return "OBJECT: "   . pad_make_content ( $input );
    elseif ( is_resource ( $input) )  return "RESOURCE: " . pad_make_content ( $input );
    elseif ( ! $input              )  return "EMPTY";
    else                              return "STRING: "   . pad_make_content ( $input );
    
  }

  function pad_check_range ( $input ) {

    $parts = pad_explode ($input, '..');

    if ( count ($parts) == 2 and ctype_alnum($parts[0]) and ctype_alnum($parts[1]) )
      return TRUE;

  }

  function pad_get_range ( $input, $increment=1 ) {

    $parts = pad_explode ($input, '..');

    return range ( $parts[0], $parts[1], $increment );

  }


  function pad_function_type ( $check ) {

    if     ( pad_file_exists ( APP . "functions/$check.php" ) ) return 'app';
    elseif ( pad_file_exists ( PAD . "functions/$check.php" ) ) return 'pad';
    elseif ( function_exists ( $check                       ) ) return 'php';
    else                                                        return pad_error ('Function not found: ' . $check);

  }


  function pad_function_in_tag ( $type, $name, $self, $parm ) {

    $fun [1] [0] = 'TYPE';
    $fun [1] [1] = 'OPR';

    if ( $type )
      $fun [1] [2] = $type;
    else
      $fun [1] [2] = 'function_' . pad_function_type ($name);

    $fun [1] [3] = $name;
    $fun [1] [5] = 2 + count($parm);

    foreach ( $parm as $pad_k => $pad_v )
      $fun [2+$pad_k] [0] = $pad_v;

    pad_eval_type (1, 0, $fun, $self, 1); 

    return $fun [1] [0];

  }

 
  function pad_tag_as_function ( $type, $between ) {

    $GLOBALS['pad_lvl']++;
    $pad_lvl = $GLOBALS['pad_lvl'];

    $GLOBALS['pad_walks']       [$pad_lvl] = '';
    $GLOBALS['pad_current']     [$pad_lvl] = [];
    $GLOBALS['pad_base']        [$pad_lvl] = '';
    $GLOBALS['pad_occur']       [$pad_lvl] = 0;
    $GLOBALS['pad_result']      [$pad_lvl] = '';
    $GLOBALS['pad_html']        [$pad_lvl] = '';
    $GLOBALS['pad_db']          [$pad_lvl] = '';
    $GLOBALS['pad_db_lvl']      [$pad_lvl] = [];
    $GLOBALS['pad_save_vars']   [$pad_lvl] = [];
    $GLOBALS['pad_delete_vars'] [$pad_lvl] = [];

    foreach ($GLOBALS as $key => $value)
      if ( substr($key, 0, 3) == 'pad' )
        $$key = $value;    
  
    $pad_between = $between;
    include PAD . 'level/parms1.php';

    $pad_tag_type     = $type;
    $pad_content      = '';
    $pad_false        = '';
    $pad_pair         = FALSE;
    $pad_name         = $pad_tag;
    $pad_options_done = [];
    $pad_walk         = 'start';

    $pad_data [$pad_lvl] [1] = [];

    include PAD . 'level/parms2.php';

    $result = include PAD . "level/type.php";

    if ( in_array ( $pad_walk, ['end', 'occurence'] ) ) {
      $pad_base [$pad_lvl] = $pad_html [$pad_lvl] = $pad_result [$pad_lvl] = $pad_content;
      $result = include PAD . "level/type.php";
    }

    foreach ( $GLOBALS['pad_parameters'] [$pad_lvl-1] as $pad_k => $pad_v )
      $GLOBALS[$pad_k] = $pad_v;    

    $GLOBALS['pad_lvl']--;

    if ( ! pad_is_default_data ( $pad_data [$pad_lvl] ) )
      return $pad_data [$pad_lvl];
    elseif ( $pad_content !== '' )
      return $pad_content;
    else
      return $result;

  } 


  function pad_make_flag ( $input ) {

    if     ( $input === NULL  )  return FALSE;
    elseif ( $input === FALSE )  return FALSE;
    elseif ( $input === TRUE  )  return TRUE;

    if ( is_array ($input) or is_object ($input) or is_resource ($input) )  {

      $array = pad_xxx_to_array ( $input );

      if ( pad_is_default_data ( $array )  )
        return FALSE;

      if ( count ( $array ) )
        return TRUE; 
      else
        return FALSE;

    }
 
    if ( pad_eval($input) )
      return TRUE; 
    else
      return FALSE;

  }


  function pad_make_content ( $input ) {    

    if     ( $input === NULL        )  return '';
    elseif ( $input === FALSE       )  return '';
    elseif ( $input === TRUE        )  return '1';
    elseif ( is_array ( $input )    )  return pad_array_to_string ( $input );
    elseif ( is_object ( $input )   )  return pad_array_to_string ( $input );
    elseif ( is_resource ( $input ) )  return pad_array_to_string ( $input );
    else                               return $input; 

  }

  function pad_array_to_string ( $input ) {    

    $array = pad_make_array ( $input );

    foreach ( $array as $key => $value )
      $array [$key] = pad_make_content ( $value );

    return trim ( implode (' ', $array) );

  }


 function pad_make_array ( $input ) {      

    if     ( $input === NULL       )  return [];
    elseif ( $input === FALSE      )  return [];
    elseif ( $input === TRUE       )  return [1 => 1 ];
    elseif ( is_array ( $input)    )  return $input;
    elseif ( is_object ( $input)   )  return pad_xxx_to_array ( $input );
    elseif ( is_resource ( $input) )  return pad_xxx_to_array ( $input );
    elseif ( ! trim($input)        )  return [];
    else                              return [1 => trim($input) ];      

  }



  function pad_callback_before_xxx ($pad_callback_type) {

    $pad_vars_before = [];
    foreach ($GLOBALS as $pad_k => $pad_v)
      if ( pad_valid_store ($pad_k) ) { 
        $pad_vars_before [] = $pad_k;
        $$pad_k = $pad_v;
      }

    include PAD . "callback/$pad_callback_type.php";

    $pad_vars_after = get_defined_vars ();

    foreach ($pad_vars_before as $pad_k => $pad_v)
      if ( isset( $GLOBALS [$pad_k] ) )
        unset( $GLOBALS [$pad_k] );

    foreach ($pad_vars_after as $pad_k => $pad_v)
      if ( pad_valid_store ($pad_k) ) {
        if ( isset( $GLOBALS [$pad_k] ) )
          unset( $GLOBALS [$pad_k] );
        $GLOBALS [$pad_k] = $$pad_k;
      }

  }

  function pad_callback_before_row ( &$pad_row_parm ) {

    if ( isset( $GLOBALS ['row'] ) ) {
      $pad_row_save = TRUE;
      $pad_row_save_store = $GLOBALS ['row'];
    } else
      $pad_row_save = FALSE;

    $pad_vars_before = [];
    foreach ($GLOBALS as $pad_k => $pad_v)
      if ( $pad_k <> 'row' and pad_valid_store ($pad_k) ) { 
        $pad_vars_before [] = $pad_k;
        $$pad_k = $pad_v;
      }

    $row = $pad_row_parm;  
    include PAD . 'callback/row.php';
    $pad_row_parm = $row;  

    $pad_vars_after = get_defined_vars();

    foreach ($pad_vars_before as $pad_k => $pad_v)
      if ( isset( $GLOBALS [$pad_k] ) )
        unset( $GLOBALS [$pad_k] );

    foreach ($pad_vars_after as $pad_k => $pad_v)
      if ( $pad_k <> 'row' and pad_valid_store ($pad_k) ) {
        if ( isset( $GLOBALS [$pad_k] ) )
          unset( $GLOBALS [$pad_k] );
        $GLOBALS [$pad_k] = $pad_v;
      }

    if ( $pad_row_save ) {
      if ( isset( $GLOBALS ['row'] ) )
        unset ( $GLOBALS ['row'] );
      $GLOBALS ['row'] = $pad_row_save_store;
    }

  }


  function pad_set_global ( $name, $value ) {

    global $pad_lvl, $pad_save_vars, $pad_delete_vars;
    
    if ( isset($GLOBALS [$name]) and ! isset ($pad_save_vars [$pad_lvl] [$name]) )
      $pad_save_vars [$pad_lvl] [$name] = $GLOBALS [$name];

    if ( ! isset ( $GLOBALS [$name] ) )
      $pad_delete_vars [$pad_lvl] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }


  function pad_reset ($start, $end=0) {

    global $pad_save_vars, $pad_delete_vars;

    if ( ! $end )
      $end = $start;
  
    for ($lvl = $end; $lvl>=$start; $lvl--)  {    

      foreach ( $pad_save_vars [$lvl] as $key => $value) {
        if ( isset ( $GLOBALS [$key] ) ) 
          unset ($GLOBALS [$key] );
        $GLOBALS [$key]= $value;
      }

      foreach ( $pad_delete_vars [$lvl] as $key)
        unset ($GLOBALS [$key]);

    }

  }


  function pad_ignore ($info) {

    if ( $GLOBALS['pad_pair'] ) 
      $tmp = $GLOBALS['pad_between'];
    else
      $tmp = $GLOBALS['pad_between'] . '/' ;
      
    pad_html  ( '&open;' . $tmp . '&close;' );

    return FALSE;
    
  }

  function pad_is_object ($item) {

    if ( isset ($GLOBALS[$item]) and is_object ($GLOBALS[$item]) )
      return TRUE;
    else
      return FALSE;

  }

  function pad_is_resource ($item) {

    if ( isset ($GLOBALS[$item]) and is_resource ($GLOBALS[$item]) )
      return TRUE;
    else
      return FALSE;

  }

  function pad_add_array_to_data ( $array, $type='') {

    global $pad_data, $pad_lvl;

    $add = pad_make_data ($array, $type);

    if ( pad_is_default_data ( $pad_data [$pad_lvl] ) )
      $pad_data [$pad_lvl] = $array;
    else
      foreach ( $add as $value )
        $pad_data [$pad_lvl] [] = $value;

  }

  function pad_default_data () {
    
    return [ 999 => [] ];

  }

  function pad_is_default_data ( $data ) {
    
    if ( ! is_array ( $data )       ) return FALSE;
    if ( count ( $data ) <> 1       ) return FALSE;
    if ( ! isset ( $data [999] )    ) return FALSE;
    if ( ! is_array ( $data [999] ) ) return FALSE;
    if ( count ( $data [999] )      ) return FALSE;

    return TRUE;

  }


  function pad_chk_level_array ($tag) {

    global $pad_current, $pad_lvl;

    for ( $search = $pad_lvl; $search>1; $search-- )
      if ( isset ( $pad_current [$search] [$tag] ) and is_array ( $pad_current [$search] [$tag]) )
        return TRUE;

    return FALSE;

  }

  function pad_get_level_array ($tag) {

    global $pad_current, $pad_lvl;

    for ( $search = $pad_lvl; $search>1; $search-- )
      if ( isset ( $pad_current [$search] [$tag] ) and is_array ( $pad_current [$search] [$tag]) )
        return $pad_current [$search] [$tag];

  }


  function pad_tag_error ($error='') {

    if ($error)
      return pad_error ($error);
    else
      return pad_error ("PAD tag syntax error");

  }


  function pad_check_tag ($tag, $string) {

    return ( substr_count($string, "{" . $tag) == substr_count($string, "{/" . $tag) ) ;

  }
  

  function pad_check_value (&$value) {

    if     ($value === NULL)      $value = '';
    elseif ($value === TRUE)      $value = '1';
    elseif ($value === FALSE)     $value = '';
    elseif (is_array($value))     $value = '';
    elseif (is_object($value))    $value = '';
    elseif (is_resource($value))  $value = '';
    
  }


  function pad_true_false ($analyse) {

    if     ( $analyse === NULL         ) return FALSE;
    elseif ( $analyse === FALSE        ) return FALSE;
    elseif ( $analyse === TRUE         ) return TRUE;
    elseif ( is_object    ( $analyse ) ) return FALSE;
    elseif ( is_resource  ( $analyse ) ) return FALSE;
    elseif ( is_array     ( $analyse ) ) 
      if ( count($analyse) )
        return TRUE;
      else
        return FALSE;
    else
      if ( trim($analyse) )
        return TRUE;
      else
        return FALSE;

  }


  function pad_analyze_var ($analyse) {

    if     ( $analyse === NULL         ) return 'null'; 
    elseif ( $analyse === FALSE        ) return 'false';
    elseif ( $analyse === TRUE         ) return 'true';
    elseif ( is_array     ( $analyse ) ) return 'array:'       . count                ($analyse);
    elseif ( is_object    ( $analyse ) ) return 'object:'      . get_class            ($analyse);
    elseif ( is_resource  ( $analyse ) ) return 'resource:'    . get_resource_type    ($analyse) ;
    elseif ( is_integer   ( $analyse ) ) return 'integer'      . pad_analyze_var_info ($analyse);
    elseif ( is_float     ( $analyse ) ) return 'float'        . pad_analyze_var_info ($analyse);
    elseif ( is_double    ( $analyse ) ) return 'double'       . pad_analyze_var_info ($analyse);
    elseif ( is_bool      ( $analyse ) ) return 'bool'         . pad_analyze_var_info ($analyse);
    elseif ( ctype_alpha  ( $analyse ) ) return 'alphabetic'   . pad_analyze_var_info ($analyse);
    elseif ( ctype_digit  ( $analyse ) ) return 'numeric'      . pad_analyze_var_info ($analyse);
    elseif ( ctype_xdigit ( $analyse ) ) return 'hexadecimal'  . pad_analyze_var_info ($analyse);
    elseif ( ctype_alnum  ( $analyse ) ) return 'alphanumeric' . pad_analyze_var_info ($analyse);
    elseif ( is_string    ( $analyse ) ) return 'string'       . pad_analyze_var_info ($analyse);
    else                                 return 'other'        . pad_analyze_var_info ($analyse);

  }

  function pad_analyze_var_info ($analyse) {

     $work = $analyse;
     $work = trim(preg_replace('/\s+/', ' ', $work));
     $work = substr($work, 0, 50);

     return ':' . $work;

  }


  function pad_tag_parm ($parm, $default='') {

    global $pad_parms_tag;

    if ( isset ( $pad_parms_tag [$parm] ) )
      return $pad_parms_tag [$parm];
    else
      return $default;

  }


  function pad_set_arr_var ($arr, $var, $val) {

    global $pad_lvl, $pad_parameters;

    $GLOBALS ["pad_$arr"] [$var] = $pad_parameters [$pad_lvl] [$arr] [$var] = $val;

  }   


  function pad_var_opts ($val, $opts) {
  
    global $pad_opts_trace, $pad_trace, $pad_fld_cnt;

    if ($pad_trace)
      $pad_opts_trace = [];

    foreach($opts as $opt) {
        
      $save = $val;

      $append  = (substr($opt, 0, 1) == '.');
      $prepend = (substr($opt, -1)   == '.');
  
      if ($append)   $opt = trim(substr($opt,1));
      if ($prepend)  $opt = trim(substr($opt,0,-1));
  
      $now = (substr($opt, 0, 1) == '%') ? sprintf($opt, $val) : pad_eval ($opt, $val);
     
      if ( $append )                  $val = $val . $now;
      if ( $prepend )                 $val = $now . $val;
      if ( ! $append and ! $prepend ) $val = $now;

      if ($pad_trace and $val <> $save)
        $pad_opts_trace [$opt] = $val;

    }

    return $val;
    
  }

  
  function pad_content_type (&$content) {

    $content = trim ( $content );

    if ( substr ($content, 0, 5) == '%YAML' )
      $type = 'yaml';
    elseif ( substr ($content, 0, 3) == '---' )
      $type = 'yaml';
    elseif ( substr ( $content, 0, 5) == '<?xml')
      $type = 'xml';
    elseif ( strpos ( $content, '<!DOCTYPE') !== FALSE ) {
      $open   = strpos  ($content, '<!DOCTYPE');
      $close  = strpos  ($content, '>', $open);
      $check  = stripos ($content, 'html', $open);
      if ($check !== FALSE and $check < $close )
        $type = 'html';
      else
        $type = 'xml';
    }
    elseif ( substr ($content, 9, 5) == '<html' )
      $type = 'html';
    elseif ( substr($content, 0, 1) == '<')
      $type = 'xml';
    elseif ( substr($content, 0, 1) == '{')
      $type = 'json';
    elseif ( substr($content, 0, 1) == '[')
      $type = 'json';
    elseif ( substr($content, 0, 1) == '(')
      $type = 'json';
    elseif ( substr($content, -1) == ')')
      $type = 'json';
    else
      $type = '';
      
    if ( $type )
      return $type;

    $first = strpos ($content, '({');
    $last  = strpos ($content, '})');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return $type;
    }

    $first = strpos ($content, '([');
    $last  = strpos ($content, '}]');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return $type;
    }

  }


  function pad_arr_to_html ( $arr ) {

      $p = htmlentities ( print_r ( $arr, TRUE ) ) ;

      $p = str_replace(" =&gt; Array\n" ,"\n", $p);
      $p = str_replace(")\n\n" ,")\n", $p);

      $p = preg_replace("/[\n]\s+\(/", "", $p);
      $p = preg_replace("/[\n]\s+\)/", "", $p);

      $p = '<pre>'.substr($p, 8, strlen($p) - 10).'</pre>';

      $p = str_replace('{', '&open;',  $p);
      $p = str_replace('}', '&close;', $p);

      return "<table border=1><tr><td>$p</td></tr></table>";
      
  }


  function pad_get_html ($file, $call=false) {

    global $pad_build_mode;

    $html = '';

    if ( $pad_build_mode== 'isolate' )
      $html .= '{isolate}';    

    if ( $call or $pad_build_mode == 'demand' or $pad_build_mode == 'isolate' )
      $html .= "{call '" . str_replace ( '.html', '.php', $file ) . "'}";    

    $html .= pad_file_get_contents ($file);
      
    if ( $pad_build_mode== 'isolate' )
      $html .= '{/isolate}';    

    return $html;

  }


  function pad_build_location ( $location, $data ) {

    if ( $GLOBALS['pad_build_location'] )
      if ( $data )
        return "{true '$location'}" . $data . '{/true}';
      else
        return "{false '$location' /}";
    else
      return $data;    
  
  }


  
  function pad_valid_store ($fld) {

    if ( substr($fld, 0, 4) == 'pad_')
      return FALSE;

    if ( in_array ( $fld, ['GLOBALS','_POST','_GET','_COOKIE','_SESSION','_FILES','_SERVER','_REQUEST','_ENV'] ) )
      return FALSE;

    if ( in_array ( $fld, ['app', 'pad', 'page', 'PADSESSID', 'PADREQID', 'PHPSESSID'] ) )
      return FALSE;

    return TRUE;


  }

  function pad_data_filter_go (&$vars, $start, $end) {

    $now = 0;
    foreach ( $vars as $key => $value ) {
      $now++;
      if ($now < $start or $now > $end)
        unset($vars [$key]);
    }

  }

  
?>