<?php


  function p () {

    return $GLOBALS ['pad'];
  
  }


  function padLocal () {

    if ( ! isset($GLOBALS ['padLocal']) )
      return FALSE;
    
    $host = strtolower(trim($_SERVER['HTTP_HOST']??''));
    $ip   = $_SERVER ['REMOTE_ADDR'] ?? '';
    $name = $_SERVER ['SERVER_NAME'] ?? '';

    if ( in_array ( $host, $GLOBALS ['padLocal'] ) ) return TRUE;
    if ( in_array ( $ip,   $GLOBALS ['padLocal'] ) ) return TRUE;
    if ( in_array ( $name, $GLOBALS ['padLocal'] ) ) return TRUE;

    return FALSE;
    
  }


  function padExplode ( $haystack, $limit, $number=0 ) {

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

    // if ( isset($GLOBALS ['padTrace']) and $GLOBALS ['padTrace'] ) 
    //   padFilePutContents ( 
    //     $GLOBALS ['padOccurDir'][p()] . '/explode/' . padRandomString() . '.json',
    //     [ $haystack, $limit, array_values ( $explode ) ]
    //   );

    return array_values ( $explode );
    
  }


  function padJson ($data) {

    return json_encode ( $data, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK

    );

  }


  function padEmptyBuffers () {

    $buffers = ob_get_level ();

    for ($i = 1; $i <= $buffers; $i++)
      $buffer = ob_get_clean();

  }


  function padToArray($xxx) {

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
  
      
  function padHeader ($header) {

    if ( headers_sent () )
      return;

    $GLOBALS ['padHeaders'] [] = $header;
 
    header ($header);
  
  }


  function padFieldName ($parm) {
    
    return (substr($parm, 0, 1) == '$') ? substr($parm, 1) : $parm;

  }


  function padCheckPage ( $app, $page ) {

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
    $padart     = padExplode ($page, '/');
    
    foreach ($padart as $key => $value) {
      
      if ($value == 'inits') return FALSE;
      if ($value == 'exits') return FALSE;

      if ( $key == array_key_last($padart)
            and (file_exists("$location/$value.php") or file_exists("$location/$value.html") ) )
        return TRUE; 
      elseif ( is_dir ("$location/$value") )
        $location.= "/$value";
      else
        return FALSE;
      
    }
    
    return ( file_exists("$location/index.php") or file_exists("$location/index.html") );
    
  }


  function padGetPage ( $app, $page ) {

    $location = APPS . "$app/pages";
    $padart     = padExplode ($page, '/');
    
    foreach ($padart as $key => $value)
      if ( $key == array_key_last($padart)
            and (file_exists("$location/$value.php") or file_exists("$location/$value.html") ) )
        return $page; 
      elseif ( is_dir ("$location/$value") )
        $location.= "/$value";
   
    return "$page/index";

  }
  
  function padCloseHtml () {

    echo "\r\n";

    for ($i = 1; $i <= 25; $i++)
      echo "</pre></div></td></tr></th></table></font></span></blockquote></h1></h2></h3></h4></h5></h6></b></i></u></p></ul></li></ol></dl></dt></dd>\r\n";
    for ($i = 1; $i <= 25; $i++)
      echo "</pre></div></td></tr></th></table></font></span></blockquote></h1></h2></h3></h4></h5></h6></b></i></u></p></ul></li></ol></dl></dt></dd>\r\n";

  }


  function padMd5 ($input) {
    return substr(padBase64(padPack(md5($input))),0,22);
  }
  
  function padMd5Unpack ($input) {
    return padUnpack(padUnbase64 ($input.'=='));
  }

  function padPack ($data) {
    return pack('H*',$data);
  }

  function padUnpack ($data) {
    return unpack('H*',$data)[1];
  }

  function padBase64 ($string) {
    return strtr(base64_encode($string),'+/','_-');
  }

  function padUnbase64 ($string) {
    return base64_decode(strtr($string,'_-','+/'));
  }

  function padRandomString ($len=8) {
    $random = ceil(($len/4)*3);
    $random = random_bytes($random);
    $random = base64_encode($random);
    $random = substr($random,0,$len);
    $random = str_replace ( '+', padRandomChar(), $random );
    $random = str_replace ( '/', padRandomChar(), $random );
    return $random;
  }

  function padRandomChar () {
    $random = mt_rand(0,61);
    return ($random < 10) ? chr($random+48) : ($random < 36 ? chr($random+55) : chr($random+61));
  }


  function padValid ($name) {

    if ( trim($name) == '' ) 
      return FALSE;

    if ( ! preg_match('/^[a-zA-Z_][:#a-zA-Z0-9_]*$/',$name) )
      return FALSE;

    return TRUE;  

  }


  function padUnescape ( $string ) {
    return str_replace ( ['&open;','&close;','&pipe;', '&eq;','&comma;'], ['{','}','|','=',','], $string );
  }
  function padEscape ( $string ) {
    return str_replace ( ['{','}','|','=',','], ['&open;','&close;','&pipe;', '&eq;', '&comma;'],  $string );
  }


  function padHtml ($html) {

    global $padHtml, $padStart, $padEnd, $pad;

    $padHtml [$pad] = 
    substr($padHtml [$pad], 0, 
      $padStart [$pad])
                     . $html
                     . substr($padHtml [$pad], $padEnd [$pad]+1);
    
  }
  
  

  function padZip ($data) {

    return gzencode($data);

  }


  function padUnzip ($data) {

    return gzdecode($data);

  }
  
  
  function padDuration ( $start, $end=0 ) {

    if ($end)
      $duration = (int) ( ( $end            - $start ) * 1000000 );
    else
      $duration = (int) ( ( microtime(true) - $start ) * 1000000 );

    if (!$duration)
      $duration = 1;

    return $duration;

  }


  function padBetween ($content, $start, $end) {

    $pad1 = strpos($content, $start);
    
    if ( $pad1 !== FALSE ) {
      $pad1 += strlen($start);
      $pad2 = strpos($content, $end, $pad1);
        if ( $pad2 !== FALSE)
          return substr ($content, $pad1, $pad2-$pad1);
    }
    
    return "";
    
  }


  function padCloseSession () {

    if ( ! isset($GLOBALS ['padSessionStarted']) )
      return;

    foreach ( $GLOBALS ['padSessionVars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }
  
  function padCheckRange ( $input ) {

    $parts = padExplode ($input, '..');

    if ( count ($parts) == 2 and ctype_alnum($parts[0]) and ctype_alnum($parts[1]) )
      return TRUE;

  }

  function padGetRange ( $input, $increment=1 ) {

    $parts = padExplode ($input, '..');

    return range ( $parts[0], $parts[1], $increment );

  }


  function padFunctionType ( $check ) {

    if     ( file_exists ( APP . "functions/$check.php" ) ) return 'app';
    elseif ( file_exists ( PAD . "functions/$check.php" ) ) return 'pad';
    elseif ( function_exists ( $check                   ) ) return 'php';
    else                                                    return padError ('Function not found: ' . $check);

  }


  function padFunctionInTag ( $type, $name, $self, $parm ) {

    if ( $type )
      $fun [1] [0] = $type;
    else
      $fun [1] [0] = 'function_' . padFunctionType ($name);

    $fun [1] [1] = 'TYPE';

    $fun [1] [2] = $name;
    $fun [1] [3] = 2 + count($parm);

    foreach ( $parm as $padK => $padV )
      $fun [2+$padK] [0] = $padV;

    padEvalType (1, 0, $fun, $self, 1, 999999); 

    return $fun [1] [0];

  }

 
  function padMakeFlag ( $input ) {

    if     ( $input === NULL  )  return FALSE;
    elseif ( $input === FALSE )  return FALSE;
    elseif ( $input === TRUE  )  return TRUE;

    if ( is_array ($input) or is_object ($input) or is_resource ($input) )  {

      $array = padToArray( $input );

      if ( padIsDefaultData ( $array )  )
        return FALSE;

      if ( count ( $array ) )
        return TRUE; 
      else
        return FALSE;

    }
 
    if ( padEval($input) )
      return TRUE; 
    else
      return FALSE;

  }


  function padMakeContent ( $input ) {    

    if     ( $input === NULL        )  return '';
    elseif ( $input === FALSE       )  return '';
    elseif ( $input === TRUE        )  return '1';
    elseif ( is_array ( $input )    )  return padArrayToString ( $input );
    elseif ( is_object ( $input )   )  return padArrayToString ( $input );
    elseif ( is_resource ( $input ) )  return padArrayToString ( $input );
    else                               return $input; 

  }

  function padArrayToString ( $input ) {    

    $array = padMakeArray ( $input );

    foreach ( $array as $key => $value )
      $array [$key] = padMakeContent ( $value );

    return trim ( implode (' ', $array) );

  }


 function padMakeArray ( $input ) {      

    if     ( $input === NULL       )  return [];
    elseif ( $input === FALSE      )  return [];
    elseif ( $input === TRUE       )  return [1 => 1 ];
    elseif ( is_array ( $input)    )  return $input;
    elseif ( is_object ( $input)   )  return padToArray( $input );
    elseif ( is_resource ( $input) )  return padToArray( $input );
    elseif ( ! trim($input)        )  return [];
    else                              return [1 => trim($input) ];      

  }

  function padSetGlobal ( $name, $value ) {

    if ( substr($name, 0, 3) == 'pad' )
      return;

    global $pad, $padSaveVars, $padDeleteVars;
    
    if ( array_key_exists($name, $GLOBALS) and ! array_key_exists ($name, $padSaveVars [$pad]) )
      $padSaveVars [$pad] [$name] = $GLOBALS [$name];

    if ( ! array_key_exists ($name,  $GLOBALS) )
      $padDeleteVars [$pad] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }

  function xpadSetGlobal ( $name, $value ) {

    global $pad, $padSaveVars, $padDeleteVars;
    
    if ( isset($GLOBALS [$name]) and ! isset ($padSaveVars [$pad] [$name]) )
      $padSaveVars [$pad] [$name] = $GLOBALS [$name];

    if ( ! isset ( $GLOBALS [$name] ) )
      $padDeleteVars [$pad] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }


  function padReset ($lvl) {

    global $padSaveVars, $padDeleteVars;

    foreach ( $padSaveVars [$lvl] as $key => $value) {
      if ( isset ( $GLOBALS [$key] ) ) 
        unset ($GLOBALS [$key] );
      $GLOBALS [$key]= $value;
    }

    foreach ( $padDeleteVars [$lvl] as $key)
      if ( isset ( $GLOBALS [$key] ) )
        unset ( $GLOBALS [$key] );

  }


  function padIgnore ($info) {

    global $pad, $padBetween, $padIgnCnt, $padTrace, $padLevelDir, $padIgnored;

    $pad--;
    
    $padIgnCnt++;

    $padIgnored [$padIgnCnt] [] = "$info: $padBetween";
          
    padHtml  ( '&open;' . $padBetween . '&close;' );

    if ( $padTrace ) {
      $trace ['ignored'] = "$info: $padBetween";
      padFilePutContents ( $padLevelDir [$pad] . "/ignore.$padIgnCnt.json", $trace );
    }
    
  }

  function padIsObject ($item) {

    if ( isset ($GLOBALS[$item]) and is_object ($GLOBALS[$item]) )
      return TRUE;
    else
      return FALSE;

  }

  function padIsResource ($item) {

    if ( isset ($GLOBALS[$item]) and is_resource ($GLOBALS[$item]) )
      return TRUE;
    else
      return FALSE;

  }

  function padAddArrayToData ( $array ) {

    global $padData, $pad;

    if ( padIsDefaultData ( $padData [$pad] ) )
      $padData [$pad] = $array;
    else
      foreach ( $array as $value )
        $padData [$pad] [] = $value;

  }

  function padDefaultData () {
    
    return [ 999 => [] ];

  }

  function padIsDefaultData ( $data ) {
    
    if ( ! is_array ( $data )       ) return FALSE;
    if ( count ( $data ) <> 1       ) return FALSE;
    if ( ! isset ( $data [999] )    ) return FALSE;
    if ( ! is_array ( $data [999] ) ) return FALSE;
    if ( count ( $data [999] )      ) return FALSE;

    return TRUE;

  }


  function padChkLevelArray ($tag) {

    global $padCurrent, $pad;

    for ( $search = $pad; $search>=0; $search-- )
      if ( isset ( $padCurrent [$search] [$tag] ) and is_array ( $padCurrent [$search] [$tag]) )
        return TRUE;

    return FALSE;

  }

  function padGetLevelArray ($tag) {

    global $padCurrent, $pad;

    for ( $search = $pad; $search>=0; $search-- )
      if ( isset ( $padCurrent [$search] [$tag] ) and is_array ( $padCurrent [$search] [$tag]) )
        return $padCurrent [$search] [$tag];

  }



  function padCheckTag ($tag, $string) {

    return ( substr_count($string, "{" . $tag) == substr_count($string, "{/" . $tag) ) ;

  }
  

  function padCheckValue (&$value) {

    if     ($value === NULL)      $value = '';
    elseif ($value === TRUE)      $value = '1';
    elseif ($value === FALSE)     $value = '';
    elseif (is_array($value))     $value = '';
    elseif (is_object($value))    $value = '';
    elseif (is_resource($value))  $value = '';
    
  }


  function padTrueFalse ($analyse) {

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


  function padInfo ($analyse) {

    if     ( $analyse === NULL         ) return 'null'; 
    elseif ( $analyse === TRUE         ) return 'true';
    elseif ( $analyse === FALSE        ) return 'false';
    elseif ( $analyse === NAN          ) return 'nan';
    elseif ( $analyse === INF          ) return 'inf';
    elseif ( is_array     ( $analyse ) ) return 'array:'       . count             ($analyse);
    elseif ( is_object    ( $analyse ) ) return 'object:'      . get_class         ($analyse);
    elseif ( is_resource  ( $analyse ) ) return 'resource:'    . get_resource_type ($analyse) ;
    elseif ( is_integer   ( $analyse ) ) return 'integer'      . padInfoVar      ($analyse);
    elseif ( is_float     ( $analyse ) ) return 'float'        . padInfoVar      ($analyse);
    elseif ( is_double    ( $analyse ) ) return 'double'       . padInfoVar      ($analyse);
    elseif ( is_bool      ( $analyse ) ) return 'bool'         . padInfoVar      ($analyse);
    elseif ( ctype_alpha  ( $analyse ) ) return 'alphabetic'   . padInfoVar      ($analyse);
    elseif ( ctype_digit  ( $analyse ) ) return 'numeric'      . padInfoVar      ($analyse);
    elseif ( ctype_xdigit ( $analyse ) ) return 'hexadecimal'  . padInfoVar      ($analyse);
    elseif ( ctype_alnum  ( $analyse ) ) return 'alphanumeric' . padInfoVar      ($analyse);
    elseif ( is_string    ( $analyse ) ) return 'string'       . padInfoVar      ($analyse);
    else                                 return 'other'        . padInfoVar      ($analyse);

  }

  function padInfoVar ($analyse) {

     $work = $analyse;
     $work = trim(preg_replace('/\s+/', ' ', $work));
     $work = substr($work, 0, 50);

     return ':' . $work;

  }


  function padTagParm ($parm, $default='') {

    global $pad, $padPrmsTag;

    if ( isset ( $padPrmsTag [$pad] [$parm] ) )
      return $padPrmsTag [$pad] [$parm];
    else
      return $default;

  }


  function padDone ($var, $val) {

    $GLOBALS ['padDone'] [p()] [$var] = $val;

  }   


  function padVarOpts ($val, $opts) {
  
    global $padOptsTrace, $padTrace, $padFldCnt;

    if ($padTrace)
      $padOptsTrace = [];

    foreach($opts as $opt) {
        
      $save = $val;

      $append  = (substr($opt, 0, 1) == '.');
      $padrepend = (substr($opt, -1)   == '.');
  
      if ($append)   $opt = trim(substr($opt,1));
      if ($padrepend)  $opt = trim(substr($opt,0,-1));
  
      $now = (substr($opt, 0, 1) == '%') ? sprintf($opt, $val) : padEval ($opt, $val);
     
      if ( $append )                  $val = $val . $now;
      if ( $padrepend )                 $val = $now . $val;
      if ( ! $append and ! $padrepend ) $val = $now;

      if ($padTrace and $val <> $save)
        $padOptsTrace [$opt] = $val;

    }

    return $val;
    
  }

  
  function padContentType (&$content) {

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


  function padArrToHtml ( $arr ) {

      $pad = htmlentities ( print_r ( $arr, TRUE ) ) ;

      $pad = str_replace(" =&gt; Array\n" ,"\n", $pad);
      $pad = str_replace(")\n\n" ,")\n", $pad);

      $pad = preg_replace("/[\n]\s+\(/", "", $pad);
      $pad = preg_replace("/[\n]\s+\)/", "", $pad);

      $pad = '<pre>'.substr($pad, 8, strlen($pad) - 10).'</pre>';

      $pad = str_replace('{', '&open;',  $pad);
      $pad = str_replace('}', '&close;', $pad);

      return "<table border=1><tr><td>$pad</td></tr></table>";
      
  }


  function padGetHtml ($file, $call=false) {

    global $padBuildMode;

    $html = '';

    if ( $padBuildMode== 'isolate' )
      $html .= '{isolate}';    

    if ( $call or $padBuildMode == 'demand' or $padBuildMode == 'isolate' )
      $html .= "{call '" . str_replace ( '.html', '.php', $file ) . "'}";    

    $html .= padFileGetContents ($file);
      
    if ( $padBuildMode== 'isolate' )
      $html .= '{/isolate}';    

    return $html;

  }

  
  function padValidStore ($fld) {

    if ( substr($fld, 0, 1) == 'p')
      return FALSE;

    if ( in_array ( $fld, ['GLOBALS','_POST','_GET','_COOKIE','_SESSION','_FILES','_SERVER','_REQUEST','_ENV'] ) )
      return FALSE;

    if ( in_array ( $fld, ['app', 'pad', 'page', 'PADSESSID', 'PADREQID', 'PHPSESSID'] ) )
      return FALSE;

    return TRUE;


  }

  function padDataFilterGo (&$vars, $start, $end) {

    $now = 0;
    foreach ( $vars as $key => $value ) {
      $now++;
      if ($now < $start or $now > $end)
        unset($vars [$key]);
    }

  }

  
?>