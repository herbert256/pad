  <?php

  function pad_build_html ($file) {

   pad_trace ('build/html', "$file.html");

    if ( file_exists("$file.html") ) 
      return "{build 'html' | '$file.php'}" . pad_get_html ("$file.html") . "{/build}";
    else
      return '';

  }

  function pad_build_php ($file) {

    pad_trace ('build/php', "$file.php");

    $GLOBALS ['pad_build_store'] ["$file.php"] = '';
    $GLOBALS ['pad_build_ob']    ["$file.php"] = '';

    if ( file_exists("$file.php") )
      return "{build 'php' | '$file.php' /}";
    else
      return '';

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


  function pad_reset ($start, $end) {

    global $pad_save_vars, $pad_delete_vars;
  
    for ($lvl = $end; $lvl>=$start; $lvl--)  {    

      foreach ( $pad_save_vars [$lvl] as $key => $value) {
        if ( $GLOBALS [$key] )
          unset ($GLOBALS [$key] );
        $GLOBALS [$key]= $value;
      }

      foreach ( $pad_delete_vars [$lvl] as $key)
        unset ($GLOBALS [$key]);

    }

  }


  function pad_build_location ($location, $html) {

    if ( $GLOBALS['pad_location_tag'])
      return "{location '$location'}" . $html . '{/location}';
    else
      return $html;    
  
  }

  function pad_raw ( $data ) {

    return str_replace ( '}', '&close;', $data );
    
  }

  function pad_ignore () {

    if ( $GLOBALS['pad_pair'] ) 
      $tmp = $GLOBALS['pad_between'];
    else
      $tmp = $GLOBALS['pad_between'] . '/' ;
      
    pad_trace ( 'ignore', '{' . $tmp . '}' );

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

    $add = pad_data ($array, $type);

    if ( pad_is_default_data ( $pad_data [$pad_lvl] ) and count ($add) )
      $pad_data [$pad_lvl] = [];

    foreach ( $add as $value)
      $pad_data [$pad_lvl] [] = $value;
    
  }

  function pad_is_default_data ( $data ) {
    
    if ( ! is_array ( $data ) )
      return FALSE;

    if ( count ( $data ) <> 1 )
      return FALSE;

    if ( ! isset ( $data [1] ) )
      return FALSE;
     
    if ( ! is_array ( $data [1] ) )
      return FALSE;

    if ( count ( $data [1] ) )
      return FALSE;

    return TRUE;

  }


  function pad_level_array ($tag) {

    global $pad_current, $pad_lvl;

    for ( $search = $pad_lvl; $search>1; $search--  ) {

      if ( isset ( $pad_current [$search] [$tag] ) and is_array ( $pad_current [$search] [$tag]) ) {

        $GLOBALS['pad_level_array'] [$pad_lvl+1] = $pad_current [$search] [$tag];

        return TRUE;

      }

    }

    return FALSE;

  }


  function pad_syntax_error ($error='') {

    pad_ignore();

    return pad_error ( $error );

  }


  function pad_tag_error ($error='') {

    if ($error)
      pad_error ($error);
    else
      pad_error ("PAD tag syntax error");

    return FALSE ;

  }

  function pad_check_tag ($tag, $string) {

    return ( substr_count($string, "{" . $tag) == substr_count($string, "{/" . $tag) ) ;

  }
  

  function pad_trace ($type, $parm='', $skip=FALSE) {

    global $pad_trace, $pad_trc_cnt, $pad_trace_log, $pad_trace_hist, $pad_trace_browser, $pad_trace_file;
    global $pad_lvl, $PADREQID, $pad_lvl_cnt, $pad_trc_cnt, $pad_occur_cnt, $pad_occur;

    if ( $pad_trace == 'none')
      return;

    if ( ! $pad_trc_cnt and $pad_trace_browser )
      echo "<pre>";

    $pad_trc_cnt++;

    $lineX  = str_pad ( $pad_trc_cnt, 3, ' ', STR_PAD_LEFT );
    $lvlX   = str_pad ( $pad_lvl,     3, ' ', STR_PAD_LEFT );

    if ( isset( $pad_occur [$pad_lvl] ) and $pad_occur [$pad_lvl] and !$skip )
      $occurX = str_pad ( $pad_occur [$pad_lvl],  2, ' ', STR_PAD_LEFT );
    else
      $occurX = '  ';
    
    $typeX  = str_pad ( $type, 13);
    
    $parm = trim(preg_replace('/\s+/', ' ', $parm));
    
    if ( strlen($parm) > 100)
      $parm = substr($parm, 0, 100);
    
    $lineL = "$lvlX $occurX   $typeX $parm";
    
    if ($pad_trc_cnt > 25)
      unset ($pad_trace_hist [$pad_trc_cnt-25]);
    $pad_trace_hist [$pad_trc_cnt] = $lineL;
  
    if ( $pad_trace_file )  
      pad_file_put_contents ($GLOBALS['pad_trace_log'], "$lineX $lineL" . PHP_EOL, 1);

    if ( $pad_trace_browser )
      echo htmlentities("$lineL\n");

  }

  
  function pad_var_data ($analyse) {

    if     ( $analyse === NULL         ) return ''; 
    elseif ( $analyse === FALSE        ) return '';
    elseif ( $analyse === TRUE         ) return '1';
    elseif ( is_array     ( $analyse ) ) return (string) count($analyse);
    elseif ( is_object    ( $analyse ) ) return '';
    elseif ( is_resource  ( $analyse ) ) return '';
    else                                 return (string) $analyse;

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
    else {
      $work = trim ($analyse);
      if ( $work )
        return TRUE;
      else
        return FALSE;
    }

  }

  function pad_analyze_var ($analyse) {

    if     ( $analyse === NULL         ) return 'null'; 
    elseif ( $analyse === FALSE        ) return 'false';
    elseif ( $analyse === TRUE         ) return 'true';
    elseif ( is_array     ( $analyse ) ) return 'array:'       . count($analyse);
    elseif ( is_object    ( $analyse ) ) return 'object:'      . get_class ($analyse);
    elseif ( is_resource  ( $analyse ) ) return 'resource:'    . get_resource_type($analyse) ;
    elseif ( is_integer   ( $analyse ) ) return 'integer'      . pad_analyze_var_info ($analyse);
    elseif ( is_float     ( $analyse ) ) return 'float'        . pad_analyze_var_info ($analyse);
    elseif ( is_double    ( $analyse ) ) return 'double'       . pad_analyze_var_info ($analyse);
    elseif ( ctype_alpha  ( $analyse ) ) return 'alphabetic'   . pad_analyze_var_info ($analyse);
    elseif ( ctype_digit  ( $analyse ) ) return 'numeric'      . pad_analyze_var_info ($analyse);
    elseif ( ctype_xdigit ( $analyse ) ) return 'hexadecimal'  . pad_analyze_var_info ($analyse);
    elseif ( ctype_alnum  ( $analyse ) ) return 'alphanumeric' . pad_analyze_var_info ($analyse);
    elseif ( is_string    ( $analyse ) ) return 'string'       . pad_analyze_var_info ($analyse);
    else                                 return 'other'        . pad_analyze_var_info ($analyse);

  }

  function pad_analyze_var_info ($analyse) {

     $work = (string) $analyse;
     $work = trim(preg_replace('/\s+/', ' ', $work));
     $work = substr($work, 0, 50);

     return ':' . $work;

  }


  function pad_tag_flag ($flag) {

    global $pad_parms_tag;

    if ( isset ( $pad_parms_tag [$flag] ) )
      return TRUE;
    else
      return FALSE;

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


  function pad_idx ($search = '') {

    $pad_idx = pad_find_lvl ($search);

    if ( $pad_idx === FALSE )
      return $GLOBALS ['pad_lvl'];
 
    return $pad_idx;

  }  


  function pad_find_lvl ($search = '') {

    global $pad_lvl, $pad_parameters;

    for ($i=$pad_lvl; $i; $i--)
      if ( isset($pad_parameters [$i] ['name']) and $pad_parameters [$i] ['name'] == $search)
        return $i;

    $find = (int) $search;
 
    if ( $find < 0 ) 
      return $pad_lvl + $find;

    return FALSE;

  }


  function pad_var_opts ($val, $opts) {
  
    global $pad_trace, $pad_fld_cnt;

    foreach($opts as $opt) {
        
      $save = $val;

      $append  = (substr($opt, 0, 1) == '.');
      $prepend = (substr($opt, -1)   == '.');
  
      if ($append)   $opt = trim(substr($opt,1));
      if ($prepend)  $opt = trim(substr($opt,0,-1));
  
      $now = (substr($opt, 0, 1) == '%') ? sprintf($opt, $val) : pad_eval ($opt, $val);
     
      if ( $append )                  $val = (string) $val . $now;
      if ( $prepend )                 $val = (string) $now . $val;
      if ( ! $append and ! $prepend ) $val = (string) $now;

      if ($pad_trace and $val <> $save)
        pad_trace ("field/option", "nr=$pad_fld_cnt opt=$opt before=$save after=$val");

    }

    return $val;
    
  }

  
  function pad_content_type (&$content, &$type) {

    $content = trim ( $content );

    if ( $type == 'json' and ( substr($content, 0, 1) == '{' or substr($content, 0, 1) == '[') )
      return;
      
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
      
    if ( $type )
      return;

    $first = strpos ($content, '({');
    $last  = strpos ($content, '})');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return;
    }

    $first = strpos ($content, '([');
    $last  = strpos ($content, '}]');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return;
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


  function pad_get_parms ( $type, $parms ) {

    foreach ( $parms as $field => $value )
      if ( (!isset($GLOBALS[$field])) )
        $GLOBALS [$field] = pad_get_parms_2 ( $type, $value );

  }


  function pad_get_parms_2 ( $type, $field ) {

    if ( is_array ( $field ) )
      foreach ( $field as $key => $value )
        $field [$key] = pad_get_parms_2 ( $type, $value );
    else
      $field = trim ($field);

    return $field;

  }


  function pad_get_html ($file) {

    if ( ! file_exists($file) )
      return '';
      
    pad_timing_start ('read');
    $html = file_get_contents ($file);
    pad_timing_end ('read');
    
    $html = str_replace(['\{', '\}','\|', '\='], ['&open;','&close;','&pipe;', '&eq;'], $html);
    
    return pad_build_location ($file, $html);

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