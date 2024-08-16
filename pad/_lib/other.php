<?php


  function padCode ( $padStrCod ) {

    $GLOBALS ['padStrBox'] = FALSE;
    $GLOBALS ['padStrRes'] = FALSE;
    $GLOBALS ['padStrCln'] = FALSE;
    $GLOBALS ['padStrBld'] = 'code';

    return include pad . 'start/enter/function.php';

  }


  function padSandbox ( $padStrCod ) {

    $GLOBALS ['padStrBox'] = TRUE;
    $GLOBALS ['padStrRes'] = TRUE;
    $GLOBALS ['padStrCln'] = TRUE;
    $GLOBALS ['padStrBld'] = 'code';

    return include pad . 'start/enter/function.php'; 

  }


  function padStr (  $padStrCod, $padStrBox, $padStrRes, $padStrCln, $padStrFun ) {

    return include pad . 'start/function.php';

  }


  function padContentSet ( $base, $new ) {

    padBeforeAfter ( $base, $before, $after, '@content@' ) ;
    if ( $before and $after )
      return $before . $new . $after;

    padBeforeAfter ( $new, $before, $after, '@content@' ) ;
    if ( $before and $after )
      return $before . $base . $after;
    
    $merge = padTagParm ( 'merge', 'replace' );

    if     ( $merge == 'bottom'  ) return $base . $new;
    elseif ( $merge == 'top'     ) return $new . $base;
    elseif ( $merge == 'replace' ) return $new;
    
  }


  function padContentMerge ( &$true, &$false, $new, $condition ) {

    padBeforeAfter ( $new, $newTrue, $newFalse, '@else@' ) ;

    if ( $condition ) $true  = padContentSet ( $true,  $newTrue );
    else              $false = padContentSet ( $false, $newFalse );

  }


  function padBeforeAfter ( $input, &$before, &$after, $type ) {

    $len  = strlen ( $type );
    $list = padOpenCloseList ( $input ) ;
    $pos  = strpos ( $input, $type );

    while ( $pos !== FALSE) {
      
      if  ( padOpenCloseCount ( substr ( $input, 0, $pos ), $list ) ) {
        $before = substr ( $input, 0, $pos );
        $after  = substr ( $input, $pos+$len  );
        return;
      }
  
      $pos = strpos ( $input, $type, $pos+1 );

    }

    $before = $input;
    $after  = '';

  }


  function padFileName ( $withDir ) {

    global $padFileDir, $padFileName, $padFileTimeStamp, $padFileUniqId, $padFileExtension;

    if ( $withDir )
      $name = "$padFileDir/$padFileName";
    else
      $name = $padFileName;

    if ( $padFileTimeStamp )
      $name .= '_' . padTimeStamp ();

    if ( $padFileUniqId )
      $name .= '_' . padRandomString ( $padFileUniqId );

    $name .= '.' . $padFileExtension;

    return $name;

  }


  function padTimeStamp () {

    $now = DateTime::createFromFormat('U.u', sprintf('%.6f', microtime(TRUE)));
  
    return $now->format('YmdHisu');

  }



  function padInsideOther () {

    global $padTag, $pad;

    for ( $i=$pad; $i; $i--) {
      if ( $padTag [$i] == 'include' ) return TRUE;
      if ( $padTag [$i] == 'get'     ) return TRUE;
      if ( $padTag [$i] == 'page'    ) return TRUE;
      if ( $padTag [$i] == 'example' ) return TRUE;
    }

    return FALSE;

  }


  function padEmptyBuffers () {

    global $padBuffer;

    set_error_handler ( 'padErrorThrow' );

    try {

      $j = ob_get_level (); 
     
      for ( $i = 1; $i <= $j; $i++ ) 
        $padBuffer .= ob_get_clean ();

      return $padBuffer;

    } catch (Throwable $ignored) {

    }

    restore_error_handler ();

  }


  function padCheckBuffers () {

    $output = padEmptyBuffers ();

    if ( trim($output) )
      return padError ( "Illegal output: '$output'" );

  }


  function padStartAndClose ( $go ) {

    global $pad, $padWalk, $padPrmType;

    if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
      $padWalk [$pad] = $go;
      return TRUE;
    }

    return FALSE;

  }


  function padSingleValue ( $value ) {

    if ( is_array        ( $value ) or
         is_object       ( $value ) or
         is_resource     ( $value ) or 
         padSpecialValue ( $value ) )

      return FALSE;

    return TRUE;

  }


  function padSpecialValue ( $value ) {

    if     ( $value === FALSE ) return TRUE;  
    elseif ( $value === TRUE  ) return TRUE;     
    elseif ( $value === NULL  ) return TRUE;   
    elseif ( $value === INF   ) return TRUE;   
    elseif ( $value === NAN   ) return TRUE;   
    else                        return FALSE; 

  }


  function padContentCheck ( $content ) {

    foreach ( padDirs () as $key => $value ) {

      $file = substr (padApp, 0, -1) . $value . "_content/$content.pad";

      if ( file_exists ($file) ) 
        return TRUE;

    }

    return FALSE;

  }  


  function padContentData ( $content ) {

    foreach ( padDirs () as $key => $value ) {

      $file = substr (padApp, 0, -1) . $value . "_content/$content.pad";

      if ( file_exists ($file) ) 
        return padFileGetContents ($file);

    }

    return '';

  }  


  function padInclFileName ( $check ) {

    foreach ( padDirs () as $key => $value ) {

      $file = substr (padApp, 0, -1) . $value . "_includes/$check";

      if ( file_exists ($file) and ! is_dir($file) ) return $file;
      if ( file_exists ("$file.php")               ) return "$file.php";
      if ( file_exists ("$file.pad")               ) return "$file.pad";

    }

    return '';

  }


  function padDataFileName ( $check ) {

    foreach ( padDirs () as $key => $value ) {

      $file = substr (padApp, 0, -1) . $value . "_data/$check";

      if ( file_exists ($file) and ! is_dir($file) ) return $file;
      if ( file_exists ("$file.xml")               ) return "$file.xml";
      if ( file_exists ("$file.json")              ) return "$file.json";
      if ( file_exists ("$file.yaml")              ) return "$file.yaml";
      if ( file_exists ("$file.csv")               ) return "$file.csv";
      if ( file_exists ("$file.php")               ) return "$file.php";
      if ( file_exists ("$file.curl")              ) return "$file.curl";
      if ( file_exists ("$file.sql")               ) return "$file.sql";

    }

    return '';

  }


 function padDataFileData ( $padLocalFile ) {
  
    return include pad . 'types/go/local.php';

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

    if ( ctype_alpha ( $char) ) return TRUE;
    else                        return FALSE;
 
  }


  function padCorrectPath ( $in ) {

    return str_replace ('\\',  '/', $in );
        
  }


 function padAddIds ( $url ) {

    $url = padAddGet ( $url, 'padSesID', $GLOBALS ['padSesID'] );
    $url = padAddGet ( $url, 'padReqID', $GLOBALS ['padReqID'] );

    return $url;

  }

      
  function padTidy ( $data, $fragment=FALSE ) {

    $config = $GLOBALS ['padTidyConfig'];

    if ( $fragment 
         or isset ( $_REQUEST ['padInclude'] ) 
         or isset ( $GLOBALS  ['padInclude']  ) )
      $config ['show-body-only'] = true;

    $tidy = new tidy;
    $tidy->parseString($data, $config, 'utf8');
    $tidy->cleanRepair();

    $GLOBALS ['lastTidy'] = $tidy;

    if ( $tidy->value === NULL ) 
      return $data;
    else
      return $tidy->value;

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


  function padSplit ( $needle, $haystack ) {

    $array = explode ( $needle, $haystack, 2 );

    if ( count ($array) == 0 )
      $array [] = '';

    if ( count ($array) == 1 )
      $array [] = '';

    $array [0] = trim ( $array [0] ?? '' );
    $array [1] = trim ( $array [1] ?? '' );

    return $array;

  }


  function padID () {

    return $GLOBALS ['padReqID'] ?? uniqid (TRUE);

  }


  function padMakeSafe ( $input, $len=2048 ) {

    if ( is_array($input) or is_object($input) ) 
      $input = padJson ($input);

    $input = preg_replace('/[\x00-\x1F\x7F-\xFF]/', ' ', $input);
    $input = preg_replace('/\s+/', ' ', $input);
    
    if ( strlen($input) > $len )
      $input = substr ( $input, 0, $len );
    
    $input = trim($input);

    return $input;

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


  function padJson ( $data ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $return = json_encode ( $data, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK );
    
    } catch (Throwable $e) {

      $return = '{}';

    }

    restore_error_handler ();    

    return $return;

  }


  function padToArray ($xxx) {

    if ( is_array($xxx) )
      return ($xxx);

    set_error_handler ( function ($s, $m, $f, $l) { return; } );
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

    return str_replace ( [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;', '&else;' ], 
                         [ '{',     '}',      '|',      '=',   ',',      '@',    '@else@' ], 
                         $string );
  }
  

  function padEscape ( $string ) {

    return str_replace ( [ '{',     '}',      '|',      '=',    ',',     '@'    ], 
                         [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;' ], 
                         $string );
  }


  function padPad ($value) {

    global $padPad, $padStart, $padEnd, $pad;

    $padPad [$pad] = 
        substr ( $padPad [$pad], 0, $padStart [$pad] )
      . $value
      . substr ( $padPad [$pad],    $padEnd [$pad]+1 );
    
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


  function padBetween ( $string, $open, $close, &$before, &$between, &$after ) {

    $before = $between = $after = '';
  
    $p1 = strpos ( $string, $open         );
    $p2 = strpos ( $string, $close, $p1+1 );

    if ( $p1 === FALSE or $p2 === FALSE )
      return FALSE;

    if ( $p1 )
      $before  = substr ( $string, 0, $p1 ); 

    $between = substr ( $string, $p1+1, ($p2-$p1) - 1 );

    if ( $p2 < strlen ( $string ) )
      $after = substr ( $string, $p2+1 );

    return TRUE;

  } 


  function padGetRange ( $input, $increment=1 ) {

    $parts = padExplode ($input, '..');

    return range ( $parts[0], $parts[1], $increment );

  }


  function padGetList ( $list ) {

    $list = explode ( ';', $list );

    foreach ( $list as $key => $value)
      if ( is_numeric ($value) )
        $list [$key] = intval($value);

    return $list;

  }

  
  function padFunctionInTag ( $name, $value, $ops ) {
  
    $parms = [];
   
    foreach ( $ops as $key => $value )
      if ( $key > 0)
        $parms [] = $value;
   
    $count = count ( $parms );

    if ( file_exists ( padApp . "_functions/$name.php" ) )
      $padCall = padApp . "_functions/$name.php";
    else
      $padCall = pad . "functions/$name.php";

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
    else                               return $input; 

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


  function padIgnore ( $info ) {

    global $padBetween;

    padPad ( '&open;' . $padBetween . '&close;' );

    return FALSE;
    
  }


  function padDefaultData () {
    
    return [ 999 => [] ];

  }


  function padIsDefaultData ( $data ) {
    
    if ( ! is_array ( $data ) ) return FALSE;
    if ( count ( $data ) <> 1 ) return FALSE;

    $key = array_key_first ( $data );

    if ( ! is_array ( $data [$key] ) ) return FALSE;
    if ( count ( $data [$key] )      ) return FALSE;
    
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


  function padCheckValue ($value) {

    if     ($value === NULL)      return '';
    elseif ($value === TRUE)      return '1';
    elseif ($value === FALSE)     return '';
    elseif (is_array($value))     return '';
    elseif (is_object($value))    return '';
    elseif (is_resource($value))  return '';
    else                          return $value;                       
    
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

  
  function padContentType ( &$content ) {

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
      $check  = stripos ($content, 'pad', $open);
      if ($check !== FALSE and $check < $close )
        $type = 'pad';
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


  function padValidStore ($fld) {

    if ( substr($fld, 0,3) == 'pad' )
      return FALSE;

    if ( in_array ( $fld, ['GLOBALS','_POST','_GET','_COOKIE','_SESSION','_FILES','_SERVER','_REQUEST','_ENV'] ) )
      return FALSE;

    return TRUE;

  }


  function padStrPad ( $field ) {

    if ( str_starts_with ( $field, 'pad' ) ) 
      if ( ! str_starts_with ( $field, 'padStr' ) )
        if ( ! in_array ( $field, padStrSto) )
          if ( ! in_array ( $field, padLevelVars) )
            return TRUE;
         
    return FALSE;

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