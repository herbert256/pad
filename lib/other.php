<?php


  function padXmlToArrayIterator ( $xml ) {

    $arr = array();

    for( $xml->rewind(); $xml->valid(); $xml->next() ) {

      $val = trim ( strval ( $xml->current() ) );
      $idx = $xml->key();
      $cnt = ( array_key_exists ($idx, $arr) ) ? array_key_last ($arr [$idx]) + 1 : 0;

      if ( ! $xml->hasChildren() and ! count ($xml->current()->attributes()) ) 

        $arr [$idx] [$cnt] = $val;

      else {

        if ( $val )
          $arr [$idx] [$cnt] [$idx] = $val;

        foreach ( $xml->current()-> attributes() as $key => $val)
          if ( isset ( $arr [$idx] [$cnt] [$key] ) )
            $arr [$idx] [$cnt] ['_'.$key] = strval($val);
          else
            $arr [$idx] [$cnt] [$key] = strval($val);
        
        if ( $xml->hasChildren() )
          $arr [$idx] [$cnt] ['_children'] = padXmlToArrayIterator ($xml->current());

      }

    }

    return $arr;

  }


  function padXmlToArrayCheck ( $arr ) {

    foreach ( $arr as $key => $val ) 
      if ( is_array ($val) )
        if ( count($val) == 1 and isset ($val[0]) and ! is_array ($val[0]) )
          $arr [$key] = $val [0];
        else
          $arr [$key] = padXmlToArrayCheck ( $arr [$key] ); 

    foreach ( $arr as $key => $val ) 
      if ( $key == '_children') {
        unset ( $arr [$key] );
        foreach ( $val as $key2 => $val2)
          if ( isset ( $arr [$key2] ) )
            $arr [$key2.'_'] = $val2;
          else
            $arr [$key2] = $val2;
      }

    return $arr;
  
  }


  function padDataForcePad ($data) {

    $result = [];

    foreach ( $data as $name => $value) {
      $result [$name] ['name'] = $name;      
      $result [$name] ['value'] = $value;      
    }

    return $result;
 
  }


  function padValidFirstChar ($char) {


    if ( $char == '@'         ) return TRUE;
    if ( ctype_alpha ( $char) ) return TRUE;

    return FALSE;
 
  }


  function padAtCheck ( $item ) {

    if ( str_contains($item, '@') ) 
      return TRUE;
    
    return FALSE;

  }


  function padCargo ( $base, $new ) {

    $merge = padTagParm ('cargo');

    if     ( strpos ( $new, '@cargo@'  ) !== FALSE ) return str_replace ( '@cargo@', $base, $new );
    elseif ( strpos ( $base, '@cargo@' ) !== FALSE ) return str_replace ( '@cargo@', $new, $base );
    elseif ( $merge == 'replace'                   ) return $new;
    elseif ( $merge == 'top'                       ) return $new . $base;
    elseif ( $merge == 'bottom'                    ) return $base . $new;
    elseif ( $new                                  ) return $new;
    else                                             return $base;

  }


  function padGetTrueFalse ( $input, &$true, &$false ) {

    $true  = $input;
    $false = '';

    $list = padOpenCloseList ( $true ) ;
    $pos  = strpos ( $true, '{else}');

    while ( $pos !== FALSE) {
      
      if  ( padOpenCloseCount ( substr ( $true, 0, $pos ), $list) ) {
        $false = substr ( $true, $pos+6  );
        $true  = substr ( $true, 0, $pos );
        return;
      }
  
      $pos = strpos ( $true, '{else}', $pos+1);

    }

  }


  function padCorrectPath ( $in ) {

    return str_replace ('\\',  '/', $in );
        
  }


  function padSave ( $in ) {

    $in = str_replace ('//',  '/', $in );
    $in = str_replace ('/./', '/', $in );

    return $in;
        
  }


 function padAddIds ( $url ) {

    $url = padAddGet ( $url, 'padSesID', $GLOBALS ['padSesID'] );
    $url = padAddGet ( $url, 'padReqID', $GLOBALS ['padReqID'] );

    return $url;

  }

      
 function padTidyOutput ( $data ) {

    if ( isset ( $_REQUEST ['padInclude'] ) )
      return padTidyFragment ( $data );

   $config = $GLOBALS ['padTidyConfig'];

   return padTidy ( $data, $config );

 }


 function padTidyFragment ( $data ) {

   $config = $GLOBALS ['padTidyConfig'];

   $config ['show-body-only'] = true;

   return padTidy ( $data, $config );

 }


 function padTidy ( $data, $config ) {

   $tidy = new tidy;
   $tidy->parseString($data, $config, 'utf8');
   $tidy->cleanRepair();

   return $tidy->value;

 }





  function padDemoMode ( $page ) {

    $store = padApp . "_regression/$page.html";

    if ( padExists ($store) )
      return padFileGetContents($store);
    else
      return "NO HTTP REQUESTS ALLOWED IN DEMO MODE";

  }    


  function padDirs () {

    $padIncDirs  = padExplode ( $GLOBALS ['padDir'], '/' );
    $padIncDir   = '';
    $padIncCheck = [];
    
    foreach ( $padIncDirs as $padK => $padV ) {
      $padIncDir .= "$padV/";
      $padIncCheck [] = '/' . $padIncDir;
    }

    $padIncCheck    = array_reverse ($padIncCheck);
    $padIncCheck [] = '/';

    return $padIncCheck;

  }


  function padDir () {

    global $padPage;

    if ( str_contains ( $padPage, '/') )
      return substr ( $padPage, 0, strrpos ($padPage, '/') );
    else
      return '';

  }


  function padPath () {

    global $padDir;

    if ( ! $padDir )
      return substr ( padApp, -1 );
    else
      return padApp . $padDir;

  }


  function padAddGet ($url, $key, $val ) {
    
    $str = ( strpos ($url, '?' ) === FALSE ) ? '?' : '&';
    
    return $url . $str . $key . '=' . urlencode($val);

  }


  function padContent () {
 
    global $pad, $padTrue, $padOpt;

    if ( $padTrue [$pad] )
      return $padTrue [$pad];

    if ( $padOpt [$pad] [1] )
      return $padOpt [$pad] [1];

    return '';

  }


  function padArrayClean ( $haystack ) {
 
    foreach ( $haystack as $key => $value )

      if ( empty ( $haystack [$key] ) )
      
        unset ( $haystack [$key] );         

      elseif ( is_array ( $value ) ) {
        
        $haystack [$key] = padArrayClean ( $haystack [$key] ); 
        
        if ( count ( $haystack [$key] ) == 0 )
          unset ( $haystack [$key] );   
        
      }
          
    return $haystack;

  }


  function padOpenCloseOk ( $string, $check) {

    if ( strpos ( $string, $check ) === FALSE )
      return FALSE;

    list ( $dummy, $string ) = explode ( $check, '.' . $string . '.', 2 );

    $tags = padOpenCloseList ( $string );

    return padOpenCloseCount ( $string, $tags);

  }


  function padOpenCloseList ( $string ) {

    $tags = [];
    
    $p1 = strpos($string, '{/', 0);

    while ($p1 !== FALSE) {

      $p2 = strpos($string, '}', $p1);

      if ( $p2 !== FALSE ) {

        $p3 = strpos($string, ' ', $p1);
        if ($p3 !== FALSE and $p3 < $p2 )
          $p2 = $p3;      

        $tag = substr($string, $p1+2, $p2-$p1-2);
        if ( padValidTag ($tag) )
          $tags [$tag] = TRUE;

      }

      $p1 = strpos($string, '{/', $p1+1);

    }

    return $tags;

  }

  function padOpenCloseCount ( $string, $tags ) {

   foreach ( $tags as $tag => $dummy )
      if ( ! padOpenCloseCountOne ( $string, $tag ) )
        return FALSE;

    return TRUE;  

  }

  function padOpenCloseCountOne ( $string, $tag ) {

    if ( ( substr_count($string, '{'.$tag.' ' ) + substr_count($string, '{'.$tag.'}' ) )
           <> 
         ( substr_count($string, '{/'.$tag.' ') + substr_count($string, '{/'.$tag.'}') ) )
      return FALSE;

    return TRUE;  

  }


  function padCheckTag ($tag, $string) {

    return ( substr_count($string, "{".$tag.' ') == substr_count($string, "{/" . $tag.'}') ) ;

  }


  function padIsPagesFile ( $file ) {

    $file =  padApp . $file ;

    if ( padValidFile ( $file ) )
      if  ( ( padExists ($file) and ! is_dir($file) ) or padExists ("$file.html") or padExists ("$file.php") )
        return TRUE;

    return FALSE;

  }


  function padSplit ( $needle, $haystack ) {

    $array = explode ( $needle, $haystack, 2 );

    if ( count ($array) == 0 )
      $array [] = '';

    if ( count ($array) == 1 )
      $array [] = '';

    $array [0] = trim($array [0]);
    $array [1] = trim($array [1]);

    return $array;

  }


  function padID () {

    return $GLOBALS ['padReqID'] ?? uniqid (TRUE);

  }


  function padMakeSafe ( $input ) {

    $input = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '.', $input);
    $input = preg_replace('/\s+/', ' ', $input);
    
    if ( strlen($input) > 2048 )
      $input = substr($input, 0, 2048);
    
    $input = trim($input);

    return $input;

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

    return array_values ( $explode );
    
  }


  function padJson ($data) {

    return json_encode ( $data, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK );

  }


  function padEmptyBuffers () {

    $buffers = ob_get_level ();

    for ($i = 1; $i <= $buffers; $i++)
      ob_get_clean();

  }


  function padToArray ($xxx) {

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

    if ( ! headers_sent () )
      header ($header);

    $GLOBALS ['padHeaders'] [] = $header;
 
  }


  function padFieldName ($parm) {
    
    return (substr($parm, 0, 1) == '$') ? substr($parm, 1) : $parm;

  }

  
  function padCloseHtml () {

    echo "\r\n";

    for ($i = 1; $i <= 25; $i++)
      echo "</pre></div></td></tr></th></table></font></span></blockquote></h1></h2></h3></h4></h5></h6></b></i></u></p></ul></li></ol></dl></dt></dd>\r\n";

  }


  function padMD5 ($input) {
    return substr(padBase64(padPack(md5($input))),0,22);
  }
  
  function padMD5Unpack ($input) {
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


  function padUnescape ( $string ) {

    return str_replace ( [ '&open;','&close;','&pipe;', '&eq;','&comma;','&pickup;' ], 
                         [ '{',     '}','     |',       '=',   ',',      '@'       ], 
                         $string );
  }
  function padEscape ( $string ) {

    return str_replace ( ['{','}','|','=',',','@'], 
                         ['&open;','&close;','&pipe;', '&eq;','&comma;','&pickup;'], 
                         $string );
  }


  function padHtml ($html) {

    global $padHtml, $padStart, $padEnd, $pad;

    $padHtml [$pad] = 
        substr ( $padHtml [$pad], 0, $padStart [$pad] )
      . $html
      . substr ( $padHtml [$pad],    $padEnd [$pad]+1 );
    
  }
  

  function padZip ($data) {

    return gzencode($data);

  }


  function padUnzip ($data) {

    return gzdecode($data);

  }
  
  
  function padDuration () {

    $duration = (int) ( ( microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'] ) * 1000000 );

    return $duration;

  }


  function padCut (&$content, $start, $end) {

    $cut = '';

    $p1 = strpos($content, $start);
    $p2 = strpos($content, $end);
  
    if ( $p1 !== FALSE and $p2 !== FALSE and $p1 < $p2 ) {

      $part1 = substr ($content, 0, $p1);
      $part2 = substr ($content, $p2+strlen($end) );

      $p1 += strlen($start);

      $cut     = substr ($content, $p1, $p2-$p1);      
      $content = $part1 . $part2;

      return $cut;

    } 

    $content = '';
    return '';

  }


  function padBetween ($content, $start, $end) {

    $pad1 = strpos($content, $start);
    
    if ( $pad1 !== FALSE ) {
      $pad1 += strlen($start);
      $pad2 = strpos($content, $end, $pad1);
        if ( $pad2 !== FALSE)
          return substr ($content, $pad1, $pad2-$pad1);
    }
    
    return '';
    
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

    if     ( padExists ( padApp . "functions/$check.php" ) ) return 'app';
    elseif ( padExists ( pad    . "functions/$check.php" ) ) return 'pad';
    elseif ( function_exists ( $check                    ) ) return 'php';
    else                                                     return padError ('Function not found: ' . $check);

  }


  function padTagAsFunction ( $tag, $parms ) {

    include pad . 'page/inits.php'; 

    $padHtml [$pad] = '{' . $tag . ' ' . $parms . '}';    

    return include pad . 'page/exits.php'; 

  }

  
  function padFunctionInTag ( $name, $value, $ops ) {
  
    $parms = [];
   
    foreach ( $ops as $key => $value )
      if ( $key > 0)
        $parms [] = $value;
   
    $count = count ( $parms );

    if ( padExists ( padApp . "_functions/$name.php" ) )
      $padCall = padApp . "_functions/$name.php";
    else
      $padCall = pad  . "_functions/$name.php";

    return include pad . 'call/any.php';

  }

 
  function padMakeFlag ( $input ) {

    if     ( $input === NULL  )          return FALSE;
    elseif ( $input === FALSE )          return FALSE;
    elseif ( $input === TRUE  )          return TRUE;
    elseif ( strlen(trim($input)) == 0 ) return FALSE;

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
    elseif ( $input === TRUE       )  return [1 => 1];
    elseif ( is_array ( $input)    )  return $input;
    elseif ( is_object ( $input)   )  return padToArray($input);
    elseif ( is_resource ( $input) )  return padToArray($input);
    elseif ( ! trim($input)        )  return [];
    else                              return [1 => trim($input)];      

  }


  function padSetGlobalLvl ( $name, $value ) {

    if ( ! padValidVar($name) ) 
      return;

    if ( $value === NULL )
      $value = '';

    global $pad, $padSaveLvl, $padDeleteLvl;
    
    if ( array_key_exists($name, $GLOBALS) and ! array_key_exists ($name, $padSaveLvl [$pad]) )
      $padSaveLvl [$pad] [$name] = $GLOBALS [$name];

    if ( ! array_key_exists ($name,  $GLOBALS) )
      $padDeleteLvl [$pad] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }

  function padSetGlobalOcc ( $name, $value ) {

    if ( ! padValidVar($name) ) 
      return;

    if ( $value === NULL )
      $value = '';

    global $pad, $padSaveOcc, $padDeleteOcc;
    
    if ( array_key_exists($name, $GLOBALS) and ! array_key_exists ($name, $padSaveOcc [$pad]) )
      $padSaveOcc [$pad] [$name] = $GLOBALS [$name];

    if ( ! array_key_exists ($name,  $GLOBALS) )
      $padDeleteOcc [$pad] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }


  function padResetLvl () {

    global $pad, $padSaveLvl, $padDeleteLvl;

    foreach ( $padSaveLvl [$pad] as $key => $value) {
      if ( isset ( $GLOBALS [$key] ) ) 
        unset ($GLOBALS [$key] );
      $GLOBALS [$key] = $value;
    }

    foreach ( $padDeleteLvl [$pad] as $key)
      if ( isset ( $GLOBALS [$key] ) )
        unset ( $GLOBALS [$key] );

  }


  function padResetOcc () {

    global $pad, $padSaveOcc, $padDeleteOcc;

    foreach ( $padSaveOcc [$pad] as $key => $value) {
      if ( isset ( $GLOBALS [$key] ) ) 
        unset ($GLOBALS [$key] );
      $GLOBALS [$key] = $value;
    }

    foreach ( $padDeleteOcc [$pad] as $key)
      if ( isset ( $GLOBALS [$key] ) )
        unset ( $GLOBALS [$key] );

  }

  function padSetSet ( $name, $value ) {

    if ( ! padValidVar($name) ) 
      return;

    if ( $value === NULL )
      $value = '';

    global $pad, $padSaveSet, $padDeleteSet;
    
    if ( array_key_exists($name, $GLOBALS) and ! array_key_exists ($name, $padSaveSet [$pad]) )
      $padSaveSet [$pad] [$name] = $GLOBALS [$name];

    if ( ! array_key_exists ($name,  $GLOBALS) )
      $padDeleteSet [$pad] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }


  function padSetReset () {

    global $pad, $padSaveSet, $padDeleteSet;

    foreach ( $padSaveSet [$pad] as $key => $value) {
      if ( isset ( $GLOBALS [$key] ) ) 
        unset ($GLOBALS [$key] );
      $GLOBALS [$key] = $value;
    }

    foreach ( $padDeleteSet [$pad] as $key)
      if ( isset ( $GLOBALS [$key] ) )
        unset ( $GLOBALS [$key] );

    $padSaveSet   [$pad] = [];
    $padDeleteSet [$pad] = [];

  }


  function padIgnore ($info) {

    global $padBetween;

    $GLOBALS ['padHistory'] [] = "Ignore: $info: $padBetween";
              
    padHtml ( '&open;' . $padBetween . '&close;' );

    return FALSE;
    
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


  function padChkLevel ($tag) {

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


  function padCheckValue (&$value) {

    if     ($value === NULL)      $value = '';
    elseif ($value === TRUE)      $value = '1';
    elseif ($value === FALSE)     $value = '';
    elseif (is_array($value))     $value = '';
    elseif (is_object($value))    $value = '';
    elseif (is_resource($value))  $value = '';
    
  }


  function padTagParm ($parm, $default='') {

    global $pad, $padPrm;

    padDone ($parm);

    if ( isset ( $padPrm [$pad] [$parm] ) )
      return $padPrm [$pad] [$parm];
    else
      return $default;

  }


  function padDone ($var, $val=TRUE) {

    global $pad;
    
    $GLOBALS ['padDone'] [$GLOBALS ['pad']] [$var] = $val;

  }   


  function padVarOpts ($val, $opts) {
  
    foreach($opts as $opt) {
        
      $padAppend  = (substr($opt, 0, 1) == '.');
      $padPrepend = (substr($opt, -1)   == '.');
  
      if ($padAppend)   $opt = trim(substr($opt,1));
      if ($padPrepend)  $opt = trim(substr($opt,0,-1));
  
      $now = (substr($opt, 0, 1) == '%') ? sprintf($opt, $val) : padEval ($opt, $val);
     
      if ( $padAppend )                     $val = $val . $now;
      if ( $padPrepend )                    $val = $now . $val;
      if ( ! $padAppend and ! $padPrepend ) $val = $now;

    }

    return $val;
    
  }

  
  function padContentType (&$content) {

    $content = trim ( $content );

    if ( substr($content, 0, 1) == '(' and substr($content, -1) == ')' )
      $type = 'list';
    elseif ( substr ($content, 0, 6) == '&open;') 
      $type = 'json';
    elseif ( substr ($content, 0, 5) == '%YAML' )
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
    $last  = strpos ($content, '])');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return $type;
    }

    $parts = padExplode ($content, '..');
    if ( count ($parts) == 2 and ctype_alnum($parts[0]) and ctype_alnum($parts[1]) )
      return 'range';

    if ( str_starts_with ( strtolower ( $content ), 'http:' ) 
      or str_starts_with ( strtolower ( $content ), 'https:' )  )
      return 'curl';

    if ( padDataFileName ( $content ) )
      return 'file';

    return 'csv';

  }


  function padArrToHtml ( $var ) {

    return padVarToTxt ( $var );
  
  }

  function padVarToTxt ( $source ) {

    if ( is_array ($source) and ! count($source) )
      return;

    if ( is_array($source) )
      padDumpClean ($source);

    $return = '';
    $lines  = explode ( "\n", htmlentities ( print_r ( $source, TRUE ) ) );

    foreach ( $lines as $value )  {

      if ( ! trim($value)          ) continue;
      if ( trim($value) == '('     ) continue;
      if ( trim($value) == ')'     ) continue;
      if ( trim($value) == 'Array' ) continue;

      $value = str_replace ( '=&gt; Array', '', $value );

      $return .= "  $value\n";
   
    }

    return '<table><tr></td><pre>' . trim($return) . '</pre></td></tr></table>';

  } 

  
  function padValidStore ($fld) {

    if ( substr($fld, 0,3) == 'pad')
      return FALSE;

    if ( in_array ( $fld, ['GLOBALS','_POST','_GET','_COOKIE','_SESSION','_FILES','_SERVER','_REQUEST','_ENV'] ) )
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