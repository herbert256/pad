<?php


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
  
      $GLOBALS ['padExceptions'] [] = $error;

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

    padDumpCleanErrors ();

    $org = $error;

    if ( isset ( $GLOBALS ['padErrrorList'] ) )
      foreach ( $GLOBALS ['padErrrorList'] as $list )
        if ( $list <> $org )
          $error .= " | " . $list;

    padBootStop ( $error, $file, $line );

  }

  function dump () {

    echo ( "<div align=\"left\"><pre>" );

    padDumpFields    ( $php, $lvl, $app, $cfg, $pad, $ids, $exc, $crl );
    padDumpLines     ( "App variables", $app );
    padDumpLevel     ();
    padDumpLines     ( "Level variables", $lvl );
    padDumpLines     ( "PAD variables",   $pad );
    padDumpStack     ();

    echo ( "</pre></div>" );

    padExit ();

  }

  function padDumpGo ($info) {

    echo ( "<div align=\"left\"><pre>" );

    padDumpFields    ( $php, $lvl, $app, $cfg, $pad, $ids, $exc, $crl );
    padDumpXdebug    ();
    padDumpInfo      ( $info );
    padDumpErrors    ( $info );
    padDumpStack     ();
    padDumpLevel     ();
    padDumpLines     ( "App variables", $app );
    padDumpExeptions ( $exc );
    padDumpRequest   ();
    padDumpXXX       ( $pad, 'padSeq' );
    padDumpLines     ( "PAD variables",   $pad );
    padDumpLines     ( "Level variables", $lvl );
    padDumpCurl      ( $crl );
    padDumpLines     ( "ID's", $ids );
    padDumpSQL       ();
    padDumpHeaders   ();
    padDumpLines     ( 'Configuration', $cfg );
    padDumpLines     ( 'PHP', $php );
    padDumpFiles     ();
    padDumpFunctions ();
    padDumpGlobals   ();

    echo ( "</pre></div>" );

  }

  function padDumpCurl ( $curl ) {

    if ( ! isset ( $GLOBALS ['padCurlLast'] ) )
      return;

    padDumpLines ( 'Last Curl', $curl );

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


  function padDumpCleanErrors () {

    if ( ! isset ( $GLOBALS ['padErrrorList'] ) )
      return;

    foreach ( $GLOBALS ['padErrrorList'] as $key => $error ) 
      if ( ! trim($error) )
        unset ( $GLOBALS ['padErrrorList'] [$key] ) ;

    $GLOBALS ['padErrrorList'] = array_unique ( $GLOBALS ['padErrrorList'] );

  }


  function padDumpStack () {

    if ( isset ( $GLOBALS ['padExceptions'] ) )
      $info = ' - debug_backtrace()';
    else
      $info = '';

    padDumpStackGo ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS), $info );

    if ( ! isset ( $GLOBALS ['padExceptions'] ) )
      return;

    foreach ( $GLOBALS ['padExceptions'] as $exception )
      padDumpStackGo ( $exception->getTrace(), ' - exception' );

  }


  function padDumpExeptions ( $exc ) {

    if ( ! count ( $exc ) )
      return;

    padDumpLines ( 'Exceptions', $exc );

  } 


  function padDumpXXX (&$pad, $prefix) {

    $wrk = [];
    
    foreach ( $pad as $key => $value )
      if ( str_starts_with ( $key, $prefix ) ) {
        unset ($pad[$key]);
        $wrk [$key] = $value;
      }   

    if ( count ($wrk) )
      padDumpLines ( $prefix, $wrk );

  }


  function padDumpSQL () {

    if ( isset ( $GLOBALS ['padSqlConnect'     ] ) ) 
      padDumpLines ('MySQL-App', $GLOBALS ['padSqlConnect']      );
    
    if ( isset ( $GLOBALS ['padPadSqlConnect' ] ) ) 
      padDumpLines ('MySQL-pad', $GLOBALS ['padPadSqlConnect']  );

  }


  function padDumpHeaders () {

    $out = headers_list ();
    $pad = $GLOBALS ['padHeaders'] ?? [];

                          padDumpLines ('Headers-in',  getallheaders() );
    if ( count ( $out ) ) padDumpLines ('Headers-out', $out            );
    if ( count ( $pad ) ) padDumpLines ('Headers-PAD', $pad            );

  }


  function padDumpRequest () {

    if ( isset ( $_REQUEST ) and count ( $_REQUEST ) )
      padDumpLines ('Request variables', $_REQUEST);

  }


  function padDumpLevel () {

    global $pad;
    
    if ( ! isset ( $pad ) or $pad < 0 )
      return;

    for ( $lvl=$pad; $lvl>=0; $lvl-- ) {
      padDumpLines ("Level: $lvl", padDumpGetLevel ($lvl) );
      if ( isset ($GLOBALS ['padData'][$lvl]) )
        padDumpLines ('', $GLOBALS ['padData'][$lvl] );
    }
    
  }


  function padDumpGetLevel ($pad)  {

    if ( $pad === NULL or $pad < 0 or ! isset($pad) )
      return [];
    
    return [
      'tag'    => $GLOBALS ['padTag'] [$pad] ?? '',
      'type'   => $GLOBALS ['padType'] [$pad] ?? '',
      'name' => $GLOBALS ['padName'] [$pad] ?? '',
      'pair'   => $GLOBALS ['padPair'] [$pad] ?? '',
      'p-type' => $GLOBALS ['padPrmType'] [$pad] ?? '',
      'opt'    => $GLOBALS ['padOpt'] [$pad] ?? '',
      'prm'    => $GLOBALS ['padPrm'] [$pad] ?? '',
      'set-lvl' => $GLOBALS ['padSetLvl'] [$pad] ?? '',
      'set-occ' => $GLOBALS ['padSetOcc'] [$pad] ?? '',
      'true' => padDumpShort ($GLOBALS ['padTrue'][$pad]??''),
      'false' => padDumpShort ($GLOBALS ['padFalse'][$pad]??''),
      'base' => padDumpShort ($GLOBALS ['padBase'][$pad]??''),
      'html' => padDumpShort ($GLOBALS ['padHtml'][$pad]??''),
      'result' => padDumpShort ($GLOBALS ['padResult'][$pad]??'')
    ];

  } 


  function padDumpGlobals ( ) {

    echo ( "\n<b>GLOBALS</b>\n");

    echo htmlentities ( print_r ( $GLOBALS, TRUE ) );
 
  }


  function padDumpFunctions () {
  
    $functions = get_defined_functions ();

    padDumpLines ( 'Functions', $functions ['user'] );
    
  }  


  function padDumpFiles () {
  
    padDumpLines ( 'Included files', get_included_files () );
    
  }  


  function padDumpXdebug ( ) {

    if ( ! isset ( $GLOBALS ['padExceptions'] ) )
      return;

    foreach ( $GLOBALS ['padExceptions'] as $exception )
      if ( isset ( $exception->xdebug_message ) )
        echo '<table>' . $exception->xdebug_message . '</table>' ;

  } 


  function padDumpXinfo () {
    
    if ( ! function_exists ( 'xdebug_info' ) )
      return;

    xdebug_info ();

  }


  function padDumpPhpInfo () {
    
     phpinfo();

  }


  function padDumpStackGo ( $stack, $info ) {

    echo ( "<b>Stack$info</b>\n");
    
    foreach ( $stack as $key => $trace ) {

      extract ( $trace );

      $file     = $file     ?? '???';
      $line     = $line     ?? '???';
      $function = $function ?? '???';

      echo ( "    $file:$line - $function\n");

      unset ($file);
      unset ($line);
      unset ($function);

    }
    
  } 


  function padDumpFields ( &$php, &$lvl, &$app, &$cfg, &$pad, &$ids, &$exc, &$crl ) {

    $php = $lvl = $app = $cfg = $pad = $ids = $exc = $crl = [];

    $chk1 = [ '_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];

    $chk3 = [ 'padPage','padSesID','padReqID','padRefID','PHPSESSID' ];

    $settings = padFileGetContents(pad . 'config/config.php');

    foreach ($GLOBALS as $key => $value)

      if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t"))

        $cfg  [$key] = $value;

      elseif ( $key == 'padExceptions' )
        
        $exc [$key] = $value;

      elseif ( $key == 'padSqlConnect' )
        
        $ignored [$key] = $value;

      elseif ( $key == 'padPadSqlConnect' )
        
        $ignored [$key] = $value;

      elseif ( $key == 'padCurlLast' )
        
        $crl [$key] = $value;

      elseif ( in_array ( $key, $chk3 ) )
        
        $ids [$key] = $value;

      elseif ( in_array ( $key, $chk1 ) )
        
        $php [$key] = $value;

      elseif ( in_array ( $key, $GLOBALS['padLevelVars'] ) ) {

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


  function padDumpClean ( &$array ) {

    foreach ( $array as $key => $value )
      if ( is_array ($value) ) 
         padDumpClean ( $array [$key] );
      elseif ( is_scalar ($value) )
        $array [$key] = padDumpShort ($value);

  }


  function padDumpShort ($G) {
  
    if ( $G === NULL)
      $G = '';
  
    return substr ( preg_replace('/\s+/', ' ', $G ), 0, 150 );
  
  }  


  function padDumpLines ( $info, $source ) {

    if ( is_array ($source) and ! count($source) )
      return;

    if ( is_array($source) )
      padDumpClean ($source);

    if ($info)
      echo ( "\n<b>$info</b>\n");

    $lines = explode ( "\n", htmlentities ( print_r ( $source, TRUE ) ) );

    foreach ( $lines as $value )  {

      if ( ! trim($value)          ) continue;
      if ( trim($value) == '('     ) continue;
      if ( trim($value) == ')'     ) continue;
      if ( trim($value) == 'Array' ) continue;

      $value = str_replace ( '=&gt; Array', '', $value );

      echo "  $value\n";
   
    }

  } 


  function padDumpToDir ( $info ) {

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    $errorReporting = error_reporting (0);

    try {

      padDumpToDirGo ( $info, "dumps/" . $GLOBALS ['padPage'] . '/' . $GLOBALS ['padReqID'] . '-' . padRandomString()); 

    } catch (Throwable $e) {

      padFilePutContents ( "dumps/$id/oops.txt", "$info\n\n" . $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage() );

    }

    restore_error_handler ();
    error_reporting ( $errorReporting );

  }


  function padDumpToDirGo ( $info, $dir ) {

    padDumpFields ( $php, $lvl, $app, $cfg, $pad, $ids, $exc, $crl );

    ob_start (); padDumpInfo      ( $info );                    padDumpToDirOne ( 'info',        ob_get_clean (), $dir );
    ob_start (); padDumpErrors    ( $info );                    padDumpToDirOne ( 'errors',      ob_get_clean (), $dir );
    ob_start (); padDumpStack     ();                           padDumpToDirOne ( 'stack',       ob_get_clean (), $dir );
    ob_start (); padDumpExeptions ( $exc );                     padDumpToDirOne ( 'exception',   ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( "ID's", $ids );             padDumpToDirOne ( 'ids',         ob_get_clean (), $dir );
    ob_start (); padDumpLevel     ();                           padDumpToDirOne ( 'level',       ob_get_clean (), $dir );
    ob_start (); padDumpRequest   ();                           padDumpToDirOne ( 'request',     ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( "App variables", $app );    padDumpToDirOne ( 'app-vars',    ob_get_clean (), $dir );
    ob_start (); padDumpXXX       ( $pad, 'padSeq' );           padDumpToDirOne ( 'sequence',    ob_get_clean (), $dir );
    ob_start (); padDumpFiles     ();                           padDumpToDirOne ( 'files',       ob_get_clean (), $dir );
    ob_start (); padDumpFunctions ();                           padDumpToDirOne ( 'functions',   ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( "PAD variables",   $pad );  padDumpToDirOne ( 'pad-vars',    ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( "Level variables", $lvl );  padDumpToDirOne ( 'level-vars',  ob_get_clean (), $dir );
    ob_start (); padDumpSQL       ();                           padDumpToDirOne ( 'sql',         ob_get_clean (), $dir );
    ob_start (); padDumpHeaders   ();                           padDumpToDirOne ( 'headers',     ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( 'Configuration', $cfg );    padDumpToDirOne ( 'config',      ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( 'PHP', $php );              padDumpToDirOne ( 'php-vars',    ob_get_clean (), $dir );
    ob_start (); padDumpPhpInfo   ();                           padDumpToDirOne ( 'php-info',    ob_get_clean (), $dir );
    ob_start (); padDumpXinfo     ();                           padDumpToDirOne ( 'xdebug-info', ob_get_clean (), $dir );
    ob_start (); padDumpXdebug    ();                           padDumpToDirOne ( 'xdebug-exc',  ob_get_clean (), $dir );
    ob_start (); padDumpGlobals   ();                           padDumpToDirOne ( 'globals',     ob_get_clean (), $dir );
    ob_start (); padDumpCurl      ( $crl );                     padDumpToDirOne ( 'curl',        ob_get_clean (), $dir );

  }


  function padDumpToDirOne ( $file, $txt, $dir ) {

    if ( ! trim ( $txt ) )
      return;

    padFilePutContents ( "$dir/$file.html", "<pre>$txt</pre>" );

  }


?>