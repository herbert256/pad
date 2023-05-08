<?php


  function padDumpToFile ($file, $info='') {

    ob_start ();

    padDumpGo ( $info );

    padFilePutContents ( $file, ob_get_clean () );
        
  }


  function padDumpFromApp ($info='') {

    if ( ! padLocal () ) 
      padBootError ( "Dump not allowed: $info" );

    padDump ($info);

  } 


  function padDump ($info='') {

    set_error_handler     ( 'padDumpError' );
    set_exception_handler ( 'padDumpException' );

    try {

      padDumpTry ($info);

    } catch (Throwable $error) {
  
      padDumpProblem ( 'DUMP-CATCH: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
  
    }

  }


  function padDumpError ( $type, $error, $file, $line ) {

    padDumpProblem ( "DUMP-ERROR: $error", $file, $line );
 
  }


  function padDumpException ( $e ) {

    padDumpProblem ( 'DUMP-EXCEPTION: ' . $e->getMessage(), $e->getFile(), $e->getLine() ); 
 
  }


  function padDumpTry ($info) {

    $GLOBALS ['padInDump'] = TRUE;
    $GLOBALS ['padErrrorList'] [] = $info;

    if ( ! headers_sent () ) 
      header ( 'HTTP/1.0 500 Internal Server Error' );

    padCloseHtml ();

    padEmptyBuffers ();

    if ( padLocal () )

      padDumpGo ($info);

    else {

      padErrorLog ( "DUMP: $info" );
      echo "Error: " . padID ();

    }
     
    $GLOBALS ['padSent']   = TRUE;
    $GLOBALS ['padOutput'] = '';

    padStop (500);

  }    


  function padDumpProblem ( $error, $file, $line) {

    $org = $error;

    padDumpCleanErrors ($info);

    if ( isset ( $GLOBALS ['padErrrorList'] ) )
      foreach ( $GLOBALS ['padErrrorList'] as $list )
        if ( $list <> $org )
          $error .= " | " . $list;

    padBootStop ( $error, $file, $line );

  }


  function padDumpGo ($info) {

    echo ( "<div align=\"left\"><pre>" );

    padDumpFields   ( $php, $lvl, $app, $cfg, $pad, $ids );
    padDumpInfo     ( $info );
    padDumpErrors   ( $info );
    padDumpStack    ();
    padDumpCatch    ();
    padDumpLevel    ();
    padDumpRequest  ();
    padDumpArray    ( "App variables", $app );
    padDumpXXX      ( $pad, 'padSeq' );
    padDumpArray    ( "PAD variables",   $pad );
    padDumpArray    ( "Level variables", $lvl );
    padDumpSQL      ();
    padDumpHeaders  ();
    padDumpArray    ( "ID's", $ids );
    padDumpArray    ( 'Configuration', $cfg );
    padDumpArray    ( 'PHP', $php );
    padDumpGlobals  ();

    echo ( "</pre></div>" );

  }


  function padDumpInfo ( $info ) {

    if ( isset ( $GLOBALS ['padErrrorList'] ) )
      if ( $info and in_array ( $info, $GLOBALS ['padErrrorList'] ) )
        return;

    if ( trim($info) )
      echo ( "<hr><b>$info</b><hr><br>" ); 

  } 


  function padDumpErrors ($info) {

    if ( ! isset ( $GLOBALS ['padErrrorList'] ) )
      return;

    padDumpCleanErrors ($info);

    if ( count ( $GLOBALS ['padErrrorList'] ) == 1 ) 

      foreach ( $GLOBALS ['padErrrorList'] as $error ) 
        echo ( "<hr><b>$error</b><hr><br>" ); 

    elseif ( count ( $GLOBALS ['padErrrorList'] ) > 1 ) {

      echo ( "<b>Errors</b>\n");

      $errors = array_reverse ( $GLOBALS ['padErrrorList'] );

      foreach ( $errors as $error )
        echo ( "    $error\n" );

      echo ( "\n" );

    }

  }  


  function padDumpCleanErrors ($info) {

    if ( ! isset ( $GLOBALS ['padErrrorList'] ) )
      return;

    foreach ( $GLOBALS ['padErrrorList'] as $key => $error ) 
      if ( ! trim($error) )
        unset ( $GLOBALS ['padErrrorList'] [$key] ) ;

    $GLOBALS ['padErrrorList'] = array_unique ( $GLOBALS ['padErrrorList'] );

  }


  function padDumpCatch ( ) {

    if ( ! isset ( $GLOBALS ['padErrorCatch'] ) )
      return;

    global $padErrorCatch;

    padDumpCatchStack ( $padErrorCatch->getTrace() );

    $pad = htmlentities ( print_r ( $padErrorCatch, TRUE ) ) ;
    $pad = preg_replace("/[\n]\(/", "", $pad);
    $pad = preg_replace("/[\n]\\)/", "", $pad);
    $pad = substr($pad, 0,-1);

    echo ( "\n<b>Exception</b>\n  $pad\n");

  } 


  function padDumpCatchStack ( $stack ) {

    echo ( "<b>Stack</b>\n");
    
    foreach ( $stack as $key => $trace ) {
      extract ( $trace );
      echo ( "    $file:$line - $function\n");
    }
    
  } 


  function padDumpXXX (&$pad, $prefix) {

    $wrk = [];
    
    foreach ( $pad as $key => $value )
      if ( str_starts_with ( $key, $prefix ) ) {
        unset ($pad[$key]);
        $wrk [$key] = $value;
      }   

    if ( count ($wrk) )
      padDumpArray ( $prefix, $wrk );

  }


  function padDumpStack () {

    if ( isset ( $GLOBALS ['padErrorCatch'] ) )
      return;

    $padDebugBacktrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);

    echo ( "<b>Stack</b>\n");
    
    foreach ( $padDebugBacktrace as $key => $trace ) {
      extract ( $trace );
      echo ( "    $file:$line - $function\n");
    }
    
  }


  function padDumpSQL () {

    if ( isset ( $GLOBALS ['padSqlConnect'     ] ) ) 
      padDumpObject ('MySQL-App', $GLOBALS ['padSqlConnect']      );
    
    if ( isset ( $GLOBALS ['padPadSqlConnect' ] ) ) 
      padDumpObject ('MySQL-pad', $GLOBALS ['padPadSqlConnect']  );

  }


  function padDumpHeaders () {

    $out = headers_list ();
    $pad = $GLOBALS ['padHeaders'] ?? [];

                          padDumpArray ('Headers-in',  getallheaders() );
    if ( count ( $out ) ) padDumpArray ('Headers-out', $out            );
    if ( count ( $pad ) ) padDumpArray ('Headers-PAD', $pad            );

  }


  function padDumpRequest () {

    if ( isset ( $_REQUEST ) and count ( $_REQUEST ) )
      padDumpArray ('Request variables', $_REQUEST);

  }


  function padDumpLevel () {

    global $pad;
    
    if ( ! isset ( $pad ) or $pad < 0 )
      return;

    for ( $lvl=$pad; $lvl>=0; $lvl-- )
      padDumpArray (" Level: $lvl", padDumpGetLevel ($lvl) );

    if ( isset ( $GLOBALS ['padData'] ) and is_array ( $GLOBALS ['padData'] ) )
      for ( $lvl=$pad; $lvl>0; $lvl-- )
        if ( isset ($GLOBALS ['padData'][$lvl]) )
          padDumpArray ('Level '.$lvl, $GLOBALS ['padData'][$lvl] );
    
  }

  function padDumpGlobals () {

    echo ( "\n<b>GLOBALS</b>\n" );

    echo htmlentities ( print_r ( $GLOBALS, TRUE ) );

  }


  function padDumpShort ($G) {
  
    if ( $G === NULL)
      $G = '';
  
    return substr ( preg_replace('/\s+/', ' ', $G ), 0, 150 );
  
  }  


  function padDumpSanitize ($array) {

    foreach ($array as $key => $val)
      if ( $key == 'GLOBALS' )
        $array [$key] = '*** GLOBALS ***';
      elseif ( is_array ($val) )
        $array [$key] = padDumpSanitize ($val);
      elseif ( is_object($val) )
        $array [$key] = padDumpSanitize ( padToArray($array [$key]) );
      elseif ( is_resource($val) )
        $array [$key] = padDumpSanitize ( padToArray($array [$key]) );
      else
        $array [$key] = padDumpShort ( $val );

    return $array;

  }


  function padDumpArray ( $txt, $arr ) {

    if ( ! is_array($arr) ) {
      echo ( "\n  [$txt] => [todo, not array] \n");
      return;
    }

    echo ( "\n<b>$txt</b>");

    if ( ! count ($arr )) {
      echo ( "\n");
      return;
    }

    $arr = padDumpSanitize ($arr);

    $pad = htmlentities ( print_r ( $arr, TRUE ) ) ;

    $pad = str_replace(" =&gt; Array\n" ,"\n", $pad);
    $pad = str_replace(")\n\n" ,")\n", $pad);
    $pad = preg_replace("/[\n]\s+\(/", "", $pad);
    $pad = preg_replace("/[\n]\s+\)/", "", $pad);
    $pad = str_replace("&lt;/address&gt;\n", "&lt;/address&gt;", $pad);

    echo ( "\n" . substr($pad, 8, strlen($pad) - 10));

  }


  function padDumpObject ( $txt, $obj) {

    $pad = htmlentities ( print_r ( $obj, TRUE ) ) ;
    $pad = preg_replace("/[\n]\(/", "", $pad);
    $pad = preg_replace("/[\n]\\)/", "", $pad);
    $pad = substr($pad, 0,-1);

    echo ( "\n  [$txt] $pad");

  }


  function padDumpGetLevel ($pad)  {

    if ( $pad === NULL or $pad < 0 or ! isset($pad) )
      return [];
    
    return [
      'tag'    => $GLOBALS ['padTag'] [$pad] ?? '',
      'type'   => $GLOBALS ['padType'] [$pad] ?? '',
      'pair'   => $GLOBALS ['padPair'] [$pad] ?? '',
      'p-type' => $GLOBALS ['padPrmType'] [$pad] ?? '',
      'opt'    => $GLOBALS ['padOpt'] [$pad] ?? '',
      'prm'    => $GLOBALS ['padPrm'] [$pad] ?? '',
      'set'    => $GLOBALS ['padSet'] [$pad] ?? '',
      'true' => padDumpShort ($GLOBALS ['padTrue'][$pad]??''),
      'false' => padDumpShort ($GLOBALS ['padFalse'][$pad]??''),
      'base' => padDumpShort ($GLOBALS ['padBase'][$pad]??''),
      'html' => padDumpShort ($GLOBALS ['padHtml'][$pad]??''),
      'result' => padDumpShort ($GLOBALS ['padResult'][$pad]??''),
      'name' => $GLOBALS ['padName'] [$pad] ?? '',
      'default' => $GLOBALS ['padDefault'] [$pad] ?? '',
      'walk' => $GLOBALS ['padWalk'] [$pad] ?? '',
      'hit' => $GLOBALS ['padHit'] [$pad] ?? '',
      'null' => $GLOBALS ['padNull'] [$pad] ?? '',
      'else' => $GLOBALS ['padElse'] [$pad] ?? '',
      'array' => $GLOBALS ['padArray'] [$pad] ?? '',
      'text' => $GLOBALS ['padText'] [$pad]?? ''
    ];

  } 


  function padDumpFields ( &$php, &$lvl, &$app, &$cfg, &$pad, &$ids ) {

    $php = $lvl = $app = $cfg = $pad = $ids = [];

    $chk1 = [ '_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];

    $chk2 = [ 'padTag','padType','padPair','padTrue','padFalse','padPrm','padName','padData','padCurrent','padKey','padDefault','padWalk','padWalkData','padDone','padOccur','padStart','padEnd','padBase','padHtml','padResult','padHit','padNull','padElse','padArray','padText','padSaveVars','padDeleteVars','padSetSave','padSetDelete','padTagCnt', 'padAfter', 'padBefore', 'padBeforeData', 'padEndOptions', 'padPrmType', 'padSet', 'padGiven'];

    $chk3 = [ 'padPage','padSesID','padReqID','padRefID','PHPSESSID' ];

    $settings = padFileGetContents(pad . 'config/config.php');

    foreach ($GLOBALS as $key => $value)

      if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t"))

        $cfg  [$key] = $value;

      elseif ( in_array ( $key, $chk3 ) )
        
        $ids [$key] = $value;

      elseif ( in_array ( $key, $chk1 ) )
        
        $php [$key] = $value;

      elseif ( in_array ( $key, $chk2 ) ) {

        if ( isset($value[0]) and ! $value[0] )
          unset ($value[0]);
        
        $lvl [$key] = $value;
 
      } elseif ( substr($key, 0, 3)  == 'pad' )

        $pad [$key] = $value;

      else

        $app [$key] = $value;

    ksort($app);
    ksort($cfg);
    ksort($php);
    ksort($lvl);
    ksort($pad);

  }


?>