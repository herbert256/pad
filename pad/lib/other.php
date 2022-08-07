<?php


  function pLocal () {

    if ( ! isset($GLOBALS['pLocal']) )
      return FALSE;
    
    $host = strtolower(trim($_SERVER['HTTP_HOST']??''));
    $ip   = $_SERVER ['REMOTE_ADDR'] ?? '';
    $name = $_SERVER ['SERVER_NAME'] ?? '';

    if ( in_array ( $host, $GLOBALS['pLocal'] ) ) return TRUE;
    if ( in_array ( $ip,   $GLOBALS['pLocal'] ) ) return TRUE;
    if ( in_array ( $name, $GLOBALS['pLocal'] ) ) return TRUE;

    return FALSE;
    
  }


  function pExplode ( $haystack, $limit, $number=0 ) {

    if ($number)
      $explode = explode ( $limit, $haystack, $number );
    else
      $explode = explode ( $limit, $haystack );
    
    foreach ($explode as $key => $value ) {

      $explode[$key] = trim($value);
    
      if ( $limit == '|' ) $explode [$key] = str_replace ( '&pipe;',  '|', $explode [$key] );
      if ( $limit == '=' ) $explode [$key] = str_replace ( '&eq;',    '=', $explode [$key] );
      if ( $limit == ',' ) $explode [$key] = str_replace ( '&comma;', ',', $explode [$key] );
    
      if ( $explode[$key] === '' )
        unset ( $explode[$key] );

    }

    if ( isset($GLOBALS['pTrace_explode']) and $GLOBALS['pTrace_explode'] ) 
      pFile_put_contents ( 
        $GLOBALS['$pOccurDir'] . '/explode/' . pRandom_string() . '.json',
        [ $haystack, $limit, array_values ( $explode ) ]
      );

    return array_values ( $explode );
    
  }


  function pJson ($data) {

    return json_encode ( $data, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK

    );

  }


  function pEmpty_buffers () {

    $buffers = ob_get_level ();

    for ($i = 1; $i <= $buffers; $i++)
      $buffer = ob_get_clean();

  }


  function pXxx_to_array ($xxx) {

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
  
      
  function pHeader ($header) {

    if ( headers_sent () )
      return;

    $GLOBALS['pHeaders'] [] = $header;
 
    header ($header);
  
  }


  function pField_name ($parm) {
    
    return (substr($parm, 0, 1) == '$') ? substr($parm, 1) : $parm;

  }


  function pCheck_page ( $app, $page ) {

    if ( ! preg_match ( '/^[A-Za-z0-9]+$/', $app  ) )    return FALSE;
    if ( trim($app) == '' )                              return FALSE;

    if ( ! is_dir (APPS . $app) )
      return FALSE;

    if ( ! preg_match ( '/^[A-Za-z0-9\/_]+$/', $page ) ) return FALSE;
    if ( trim($page) == '' )                             return FALSE;

    if ( strpos($page, '//') !== FALSE)                  return FALSE;
    if ( substr($page, 0, 1) == '/')                     return FALSE;
    if ( substr($page, -1) == '/')                       return FALSE;

    $location = APPS . "$app/pages";
    $part     = pExplode ($page, '/');
    
    foreach ($part as $key => $value) {
      
      if ($value == 'inits') return FALSE;
      if ($value == 'exits') return FALSE;

      if ( $key == array_key_last($part)
            and (file_exists("$location/$value.php") or file_exists("$location/$value.html") ) )
        return TRUE; 
      elseif ( is_dir ("$location/$value") )
        $location.= "/$value";
      else
        return FALSE;
      
    }
    
    return ( file_exists("$location/index.php") or file_exists("$location/index.html") );
    
  }


  function pGet_page ( $app, $page ) {

    $location = APPS . "$app/pages";
    $part     = pExplode ($page, '/');
    
    foreach ($part as $key => $value)
      if ( $key == array_key_last($part)
            and (file_exists("$location/$value.php") or file_exists("$location/$value.html") ) )
        return $page; 
      elseif ( is_dir ("$location/$value") )
        $location.= "/$value";
   
    return "$page/index";

  }
  
  function pClose_html () {

    echo "\r\n";
    for ($i = 1; $i <= 25; $i++)
      echo "</pre></div></td></tr></th></table></font></span></blockquote></h1></h2></h3></h4></h5></h6></b></i></u></p></ul></li></ol></dl></dt></dd>\r\n";

  }


  function pMd5 ($input) {
    return substr(pBase64(pPack(md5($input))),0,22);
  }
  
  function pMd5_unpack ($input) {
    return pUnpack(pUnbase64 ($input.'=='));
  }

  function pPack ($data) {
    return pack('H*',$data);
  }

  function pUnpack ($data) {
    return unpack('H*',$data)[1];
  }

  function pBase64 ($string) {
    return strtr(base64_encode($string),'+/','_-');
  }

  function pUnbase64 ($string) {
    return base64_decode(strtr($string,'_-','+/'));
  }

  function pRandom_string ($len=8) {
    $random = ceil(($len/4)*3);
    $random = random_bytes($random);
    $random = base64_encode($random);
    $random = substr($random,0,$len);
    $random = str_replace ( '+', pRandom_char(), $random );
    $random = str_replace ( '/', pRandom_char(), $random );
    return $random;
  }

  function pRandom_char () {
    $random = mt_rand(0,61);
    return ($random < 10) ? chr($random+48) : ($random < 36 ? chr($random+55) : chr($random+61));
  }


  function pValid ($name) {

    if ( trim($name) == '' ) 
      return FALSE;

    if ( ! preg_match('/^[a-zA-Z_][:#a-zA-Z0-9_]*$/',$name) )
      return FALSE;

    return TRUE;  

  }


  function pUnescape ( $string ) {
    return str_replace ( ['&open;','&close;','&pipe;', '&eq;','&comma;'], ['{','}','|','=',','], $string );
  }
  function pEscape ( $string ) {
    return str_replace ( ['{','}','|','=',','], ['&open;','&close;','&pipe;', '&eq;', '&comma;'],  $string );
  }


  function pHtml ($html) {

    global $pHtml, $pStart, $pEnd, $p;

    $pHtml[$p] = substr($pHtml[$p], 0, $pStart[$p])
                     . $html
                     . substr($pHtml[$p], $pEnd[$p]+1);
    
  }
  
  

  function pZip ($data) {

    return gzencode($data);

  }


  function pUnzip ($data) {

    return gzdecode($data);

  }
  
  
  function pDuration ( $start, $end=0 ) {

    if ($end)
      $duration = (int) ( ( $end            - $start ) * 1000000 );
    else
      $duration = (int) ( ( microtime(true) - $start ) * 1000000 );

    if (!$duration)
      $duration = 1;

    return $duration;

  }


  function pBetween ($content, $start, $end) {

    $p1 = strpos($content, $start);
    
    if ( $p1 !== FALSE ) {
      $p1 += strlen($start);
      $p2 = strpos($content, $end, $p1);
        if ( $p2 !== FALSE)
          return substr ($content, $p1, $p2-$p1);
    }
    
    return "";
    
  }


  function pClose_session () {

    if ( ! isset($GLOBALS['pSession_started']) )
      return;

    foreach ( $GLOBALS ['pSession_vars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }
  
  function pCheck_range ( $input ) {

    $parts = pExplode ($input, '..');

    if ( count ($parts) == 2 and ctype_alnum($parts[0]) and ctype_alnum($parts[1]) )
      return TRUE;

  }

  function pGet_range ( $input, $increment=1 ) {

    $parts = pExplode ($input, '..');

    return range ( $parts[0], $parts[1], $increment );

  }


  function pFunction_type ( $check ) {

    if     ( file_exists ( APP . "functions/$check.php" ) ) return 'app';
    elseif ( file_exists ( PAD . "functions/$check.php" ) ) return 'pad';
    elseif ( function_exists ( $check                       ) ) return 'php';
    else                                                        return pError ('Function not found: ' . $check);

  }


  function pFunction_in_tag ( $type, $name, $self, $parm ) {

    if ( $type )
      $fun [1] [0] = $type;
    else
      $fun [1] [0] = 'function_' . pFunction_type ($name);

    $fun [1] [1] = 'TYPE';

    $fun [1] [2] = $name;
    $fun [1] [3] = 2 + count($parm);

    foreach ( $parm as $pK => $pV )
      $fun [2+$pK] [0] = $pV;

    pEval_type (1, 0, $fun, $self, 1, 999999); 

    return $fun [1] [0];

  }

 
  function pMake_flag ( $input ) {

    if     ( $input === NULL  )  return FALSE;
    elseif ( $input === FALSE )  return FALSE;
    elseif ( $input === TRUE  )  return TRUE;

    if ( is_array ($input) or is_object ($input) or is_resource ($input) )  {

      $array = pXxx_to_array ( $input );

      if ( pIs_default_data ( $array )  )
        return FALSE;

      if ( count ( $array ) )
        return TRUE; 
      else
        return FALSE;

    }
 
    if ( pEval($input) )
      return TRUE; 
    else
      return FALSE;

  }


  function pMake_content ( $input ) {    

    if     ( $input === NULL        )  return '';
    elseif ( $input === FALSE       )  return '';
    elseif ( $input === TRUE        )  return '1';
    elseif ( is_array ( $input )    )  return pArray_to_string ( $input );
    elseif ( is_object ( $input )   )  return pArray_to_string ( $input );
    elseif ( is_resource ( $input ) )  return pArray_to_string ( $input );
    else                               return $input; 

  }

  function pArray_to_string ( $input ) {    

    $array = pMake_array ( $input );

    foreach ( $array as $key => $value )
      $array [$key] = pMake_content ( $value );

    return trim ( implode (' ', $array) );

  }


 function pMake_array ( $input ) {      

    if     ( $input === NULL       )  return [];
    elseif ( $input === FALSE      )  return [];
    elseif ( $input === TRUE       )  return [1 => 1 ];
    elseif ( is_array ( $input)    )  return $input;
    elseif ( is_object ( $input)   )  return pXxx_to_array ( $input );
    elseif ( is_resource ( $input) )  return pXxx_to_array ( $input );
    elseif ( ! trim($input)        )  return [];
    else                              return [1 => trim($input) ];      

  }

  function pSet_global ( $name, $value ) {

    global $p, $pSave_vars, $pDelete_vars;
    
    if ( array_key_exists($name, $GLOBALS) and ! array_key_exists ($name, $pSave_vars[$p]) )
      $pSave_vars[$p] [$name] = $GLOBALS [$name];

    if ( ! array_key_exists ($name,  $GLOBALS) )
      $pDelete_vars[$p] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }

  function xpSet_global ( $name, $value ) {

    global $p, $pSave_vars, $pDelete_vars;
    
    if ( isset($GLOBALS [$name]) and ! isset ($pSave_vars[$p] [$name]) )
      $pSave_vars[$p] [$name] = $GLOBALS [$name];

    if ( ! isset ( $GLOBALS [$name] ) )
      $pDelete_vars[$p] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }


  function pReset ($lvl) {

    global $pSave_vars, $pDelete_vars;

    foreach ( $pSave_vars [$lvl] as $key => $value) {
      if ( isset ( $GLOBALS [$key] ) ) 
        unset ($GLOBALS [$key] );
      $GLOBALS [$key]= $value;
    }

    foreach ( $pDelete_vars [$lvl] as $key)
      if ( isset ( $GLOBALS [$key] ) )
        unset ( $GLOBALS [$key] );

  }


  function pIgnore ($info) {

    if ( $GLOBALS['pPair'] ) 
      $tmp = $GLOBALS['pBetween'];
    else
      $tmp = $GLOBALS['pBetween'] . '/' ;
      
    pHtml  ( '&open;' . $tmp . '&close;' );

    return FALSE;
    
  }

  function pIs_object ($item) {

    if ( isset ($GLOBALS[$item]) and is_object ($GLOBALS[$item]) )
      return TRUE;
    else
      return FALSE;

  }

  function pIs_resource ($item) {

    if ( isset ($GLOBALS[$item]) and is_resource ($GLOBALS[$item]) )
      return TRUE;
    else
      return FALSE;

  }

  function pAdd_array_to_data ( $array ) {

    global $pData, $p;

    if ( pIs_default_data ( $pData[$p] ) )
      $pData[$p] = $array;
    else
      foreach ( $array as $value )
        $pData[$p] [] = $value;

  }

  function pDefault_data () {
    
    return [ 999 => [] ];

  }

  function pIs_default_data ( $data ) {
    
    if ( ! is_array ( $data )       ) return FALSE;
    if ( count ( $data ) <> 1       ) return FALSE;
    if ( ! isset ( $data [999] )    ) return FALSE;
    if ( ! is_array ( $data [999] ) ) return FALSE;
    if ( count ( $data [999] )      ) return FALSE;

    return TRUE;

  }


  function pChk_level_array ($tag) {

    global $pCurrent, $p;

    for ( $search = $p; $search>1; $search-- )
      if ( isset ( $pCurrent [$search] [$tag] ) and is_array ( $pCurrent [$search] [$tag]) )
        return TRUE;

    return FALSE;

  }

  function pGet_level_array ($tag) {

    global $pCurrent, $p;

    for ( $search = $p; $search>1; $search-- )
      if ( isset ( $pCurrent [$search] [$tag] ) and is_array ( $pCurrent [$search] [$tag]) )
        return $pCurrent [$search] [$tag];

  }



  function pCheckTag ($tag, $string) {

    return ( substrCnt($string, "{" . $tag) == substrCnt($string, "{/" . $tag) ) ;

  }
  

  function pCheck_value (&$value) {

    if     ($value === NULL)      $value = '';
    elseif ($value === TRUE)      $value = '1';
    elseif ($value === FALSE)     $value = '';
    elseif (is_array($value))     $value = '';
    elseif (is_object($value))    $value = '';
    elseif (is_resource($value))  $value = '';
    
  }


  function pTrue_false ($analyse) {

    if     ( $analyse === NULL         ) return FALSE;
    elseif ( $analyse === FALSE        ) return FALSE;
    elseif ( $analyse === NAN          ) return FALSE;
    elseif ( $analyse === INF          ) return FALSE;
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


  function pInfo ($analyse) {

    if     ( $analyse === NULL         ) return 'null'; 
    elseif ( $analyse === TRUE         ) return 'true';
    elseif ( $analyse === FALSE        ) return 'false';
    elseif ( $analyse === NAN          ) return 'nan';
    elseif ( $analyse === INF          ) return 'inf';
    elseif ( is_array     ( $analyse ) ) return 'array:'       . count             ($analyse);
    elseif ( is_object    ( $analyse ) ) return 'object:'      . get_class         ($analyse);
    elseif ( is_resource  ( $analyse ) ) return 'resource:'    . get_resource_type ($analyse) ;
    elseif ( is_integer   ( $analyse ) ) return 'integer'      . pInfo_var      ($analyse);
    elseif ( is_float     ( $analyse ) ) return 'float'        . pInfo_var      ($analyse);
    elseif ( is_double    ( $analyse ) ) return 'double'       . pInfo_var      ($analyse);
    elseif ( is_bool      ( $analyse ) ) return 'bool'         . pInfo_var      ($analyse);
    elseif ( ctype_alpha  ( $analyse ) ) return 'alphabetic'   . pInfo_var      ($analyse);
    elseif ( ctype_digit  ( $analyse ) ) return 'numeric'      . pInfo_var      ($analyse);
    elseif ( ctype_xdigit ( $analyse ) ) return 'hexadecimal'  . pInfo_var      ($analyse);
    elseif ( ctype_alnum  ( $analyse ) ) return 'alphanumeric' . pInfo_var      ($analyse);
    elseif ( is_string    ( $analyse ) ) return 'string'       . pInfo_var      ($analyse);
    else                                 return 'other'        . pInfo_var      ($analyse);

  }

  function pInfo_var ($analyse) {

     $work = $analyse;
     $work = trim(preg_replace('/\s+/', ' ', $work));
     $work = substr($work, 0, 50);

     return ':' . $work;

  }


  function pTag_parm ($parm, $default='') {

    global $pPrmsTag[$p];

    if ( isset ( $pPrmsTag[$p] [$parm] ) )
      return $pPrmsTag[$p] [$parm];
    else
      return $default;

  }


  function pDone ($var, $val) {

    $GLOBALS ['pDone'] [$GLOBALS ['pad']] [$var] = $val;

  }   


  function pVar_opts ($val, $opts) {
  
    global $pOpts_trace, $pTrace_fields, $pFldCnt;

    if ($pTrace_fields)
      $pOpts_trace = [];

    foreach($opts as $opt) {
        
      $save = $val;

      $append  = (substr($opt, 0, 1) == '.');
      $prepend = (substr($opt, -1)   == '.');
  
      if ($append)   $opt = trim(substr($opt,1));
      if ($prepend)  $opt = trim(substr($opt,0,-1));
  
      $now = (substr($opt, 0, 1) == '%') ? sprintf($opt, $val) : pEval ($opt, $val);
     
      if ( $append )                  $val = $val . $now;
      if ( $prepend )                 $val = $now . $val;
      if ( ! $append and ! $prepend ) $val = $now;

      if ($pTrace_fields and $val <> $save)
        $pOpts_trace [$opt] = $val;

    }

    return $val;
    
  }

  
  function pContent_type (&$content) {

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


  function pArr_to_html ( $arr ) {

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


  function pGet_html ($file, $call=false) {

    global $pBuild_mode;

    $html = '';

    if ( $pBuild_mode== 'isolate' )
      $html .= '{isolate}';    

    if ( $call or $pBuild_mode == 'demand' or $pBuild_mode == 'isolate' )
      $html .= "{call '" . str_replace ( '.html', '.php', $file ) . "'}";    

    $html .= pFile_get_contents ($file);
      
    if ( $pBuild_mode== 'isolate' )
      $html .= '{/isolate}';    

    return $html;

  }

  
  function pValid_store ($fld) {

    if ( substr($fld, 0, 4) == 'pad_')
      return FALSE;

    if ( in_array ( $fld, ['GLOBALS','_POST','_GET','_COOKIE','_SESSION','_FILES','_SERVER','_REQUEST','_ENV'] ) )
      return FALSE;

    if ( in_array ( $fld, ['app', 'pad', 'page', 'PADSESSID', 'PADREQID', 'PHPSESSID'] ) )
      return FALSE;

    return TRUE;


  }

  function pData_filter_go (&$vars, $start, $end) {

    $now = 0;
    foreach ( $vars as $key => $value ) {
      $now++;
      if ($now < $start or $now > $end)
        unset($vars [$key]);
    }

  }

  
?>