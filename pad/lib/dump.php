<?php

  function padDump ( $error='' ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpTry ( $error );

    } catch (Throwable $e) {
  
      padErrorStop ( $error, $e );
  
    }

    restore_error_handler ();

    padExit ( 500 );

  }


  function padDumpTry ( $info ) {

    if ( ! headers_sent () ) 
      header ( 'HTTP/1.0 500 Internal Server Error' );

    padEmptyBuffers ( $padIgnored );

    if ( $GLOBALS ['padOutputType'] == 'web' )
      for ($i = 1; $i <= 25; $i++)
          echo "</pre></div></td></tr></th></table></font></span></blockquote></h1></h2></h3></h4></h5></h6></b></i></u></p></ul></li></ol></dl></dt></dd>\r\n";

    if     ( $GLOBALS ['padOutputType'] == 'console' )  padDumpConsole ( $info );      
    elseif ( padLocal () )                              padDumpLocal   ( $info );
    else                                                padDumpRemote  ( $info );
     
    $GLOBALS ['padSent']   = TRUE;
    $GLOBALS ['padOutput'] = '';

  }


  function padDumpConsole ( $info ) {

    echo padMakeSafe ("Error: $info", 100);
    
    echo "\nDir  : " . padDumpToDir ( $info );
    echo "\n";

  }


  function padDumpLocal ( $info ) {

    padDumpFields    ( $php, $lvl, $cfg, $pad, $ids, $trc, $pq );

    echo ( "<div align=\"left\"><pre>" );

    padDumpInfo      ( $info );
    padDumpXXX       ( $pq, 'pq' ); 
    padDumpStack     ();
    padDumpLevel     ();
    padDumpInput     ();    
    padDumpBuffer    ();
    padDumpApp       ();
    padDumpRequest   ();
    
    padDumpCurl      ( $pad );
    padDumpXXX       ( $pad, 'padBuild' );
    padDumpLines     ( "PAD variables",   $pad );
    padDumpLines     ( '$padInfo variables', $trc );
    padDumpLines     ( "Level variables", $lvl );
    padDumpLines     ( "ID's", $ids );
    padDumpSQL       ();
    padDumpHeaders   ();
    padDumpLines     ( 'Configuration', $cfg );
    //padDumpLines     ( 'PHP', $php );
    //padDumpFiles     ();
    //padDumpFunctions ();
    //padDumpGlobals   ();

    echo ( "</pre></div>" );
 
  }


  function padDumpRemote ( $info ) {

    if ( ! isset ( $GLOBALS ['padDumpToDirDone'] ) ) 
      padDumpToDir ( $info );
        
    echo "Error: " . padID ();

  }


  function padDumpInfo ( $info ) {

    if ( ! $info and isset ( $GLOBALS ['padExceptionText'] ) ) 
      echo ( "<hr><b>" . $GLOBALS ['padExceptionText'] . "</b><hr><br>" ); 
        
    if ( trim($info) )
      echo ( "<hr><b>" . htmlentities($info) . "</b><hr><br>" ); 

  } 


  function padDumpCurl ( &$pad ) {

    if ( isset ( $GLOBALS ['padCurlLast'] ) ) {
  
      padDumpLines ( "Last Curl",  $GLOBALS ['padCurlLast'] );

      unset ( $pad ['padLastCurl'] );

    }

  }


  function padDumpStack () {

    echo "<br>";
    
    if ( isset ( $GLOBALS ['padException'] ) ) { 
      padDumpStackGo ( $GLOBALS['padException']->getTrace() );
      echo "<br>";
    }

    padDumpStackGo ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS), 2 );
    echo '<br>';
    padDumpStackGo ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS), 1 );

  }


  function padDumpStackGo ( $stack, $flag=0 ) {

    foreach ( $stack as $key => $trace ) {

      extract ( $trace );

      $file     = $file     ?? $GLOBALS['padErrorFile'] ?? '???';
      $line     = $line     ?? $GLOBALS['padErrorLine'] ?? '???';
      $function = $function ?? '???';

      $error = ( str_starts_with ( $function, 'padDump' ) or str_starts_with ( $function, 'padError' ) );

      if ( $flag == 1 and ! $error ) continue;
      if ( $flag == 2 and   $error ) continue;

      if ( $file <> '???' )
        echo ( "$file:$line - $function\n");

      unset ($file);
      unset ($line);
      unset ($function);

    }
    
  } 


  function padDumpXXX (&$pad, $prefix) {

    $wrk = [];
    
    if ( $prefix == 'pq' )
      $wrk ['tag'] = $GLOBALS ['padOrg'] [  $GLOBALS['pad'] ] ?? '';

    foreach ( $pad as $key => $value )
      if ( str_starts_with ( $key, $prefix ) ) {
        unset ($pad[$key]);
        $wrk [$key] = $value;
      }   

    if ( count ($wrk) > 2 )
      padDumpLines ( $prefix, $wrk );

  }


  function padDumpSQL () {

    if ( isset ( $GLOBALS ['padSqlConnect'     ] ) ) 
      padDumpLines ('MySQL-App', $GLOBALS ['padSqlConnect']      );
    
    if ( isset ( $GLOBALS ['padSqlPadConnect' ] ) ) 
      padDumpLines ('MySQL-pad', $GLOBALS ['padSqlPadConnect']  );

  }


  function padDumpHeaders () {

    $out = headers_list ();
    $pad = $GLOBALS ['padHeaders'] ?? [];

    if ( function_exists ('getallheaders') )
      $hdr = getallheaders();
    else
      $hdr = [];

    if ( count ( $hdr ) ) padDumpLines ('Headers-in',  $hdr );
    if ( count ( $out ) ) padDumpLines ('Headers-out', $out );
    if ( count ( $pad ) ) padDumpLines ('Headers-PAD', $pad );

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
      if ( isset ($GLOBALS ['padCurrent'][$lvl]) )
        padDumpLines ('   Current', $GLOBALS ['padCurrent'][$lvl] );
    }
    
  }


  function padDumpGetLevel ($pad)  {

    if ( ! isset($pad) or $pad === NULL or $pad < 0 )
      return [];
    
    return [
      'org'     => $GLOBALS ['padOrg'] [$pad] ?? '',
      'tag'     => $GLOBALS ['padTag'] [$pad] ?? '',
      'type'    => $GLOBALS ['padType'] [$pad] ?? '',
      'name'    => $GLOBALS ['padName'] [$pad] ?? '',
      'pair'    => $GLOBALS ['padPair'] [$pad] ?? '',
      'opt'     => $GLOBALS ['padOpt'] [$pad] ?? '',
      'prm'     => $GLOBALS ['padPrm'] [$pad] ?? '',
      'base'    => padDumpShort ($GLOBALS ['padBase'][$pad]??''),
      'pad'     => padDumpShort ($GLOBALS ['padPad'][$pad]??''),
      'result'  => padDumpShort ($GLOBALS ['padResult'][$pad]??''),
      'flags'  => [ 'null' => $GLOBALS ['padNull'] [$pad] ?? '',
                    'else' => $GLOBALS ['padElse'] [$pad] ?? '',
                    'hit' => $GLOBALS ['padHit'] [$pad] ?? '',
                    'Array' => $GLOBALS ['padArray'] [$pad] ?? '']
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


  function padDumpPhpInfo () {
    
     phpinfo();

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
  
    if ( is_array ($G) )
      $G = '!!! array as input for padDumpShort !!!';
  
    return substr ( preg_replace('/\s+/', ' ', $G ), 0, 150 );
  
  }  


  function padDumpInput ( ) {

    padDumpLines ( 'Input', file_get_contents ('php://input') );

  }

  function padDumpBuffer ( ) {

    #padDumpLines ( 'Output buffer', $GLOBALS ['padBuffer'] );

  }


  function padDumpApp () {

    $app = [];

    foreach ( $GLOBALS as $k => $v )
      if ( padValidStore ($k) )
        $app [$k] = $v;

    ksort($app);

    padDumpLines ( 'App variables', $app );

  }


  function padDumpLines ( $info, $source ) {

    if ( padSingleValue ( $source ) )
      $source = trim ( $source );

    if ( is_array ($source) and ! count($source) )
      return;
    elseif ( ! $source )
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


  function padDumpFields ( &$php, &$lvl, &$cfg, &$pad, &$ids, &$inf, &$pq ) {
    
    $php = $lvl = $cfg = $pad = $ids = $inf = $pq = [];

    $chk1 = [ '_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];

    $chk3 = [ 'padPage','padSesID','padReqID','padRefID','PHPSESSID' ];
    
    $settings = padFileGet ( 'config/config.php' );

    foreach ($GLOBALS as $key => $value)

      if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t"))

        $cfg  [$key] = $value;

      elseif ( substr($key, 0, 7)  == 'padInfo' )

        $inf [$key] = $value;

      elseif ( $key == 'padSqlConnect' )
        
        $ignored [$key] = 1;

      elseif ( $key == 'padSqlPadConnect' )
        
        $ignored [$key] = 1;

      elseif ( in_array ( $key, $chk3 ) )
        
        $ids [$key] = $value;

      elseif ( in_array ( $key, $chk1 ) )
        
        $php [$key] = $value;

      elseif ( in_array ( $key, padLevelVars ) ) {

        if ( isset($value[0]) and ! $value[0] )
          unset ($value[0]);
        
        $lvl [$key] = $value;
 
      } elseif ( substr($key, 0, 3)  == 'pad' )

        $pad [$key] = $value;

       elseif ( substr($key, 0, 2)  == 'pq' )

        $pq [$key] = $value;

    ksort($inf);
    ksort($cfg);
    ksort($php);
    ksort($lvl);
    ksort($pad);

  }


  function padDumpToDir ( $info='', $dir='' ) {
 
    if ( ! $dir )
      $dir = "dumps/" . $GLOBALS ['padPage'] . '/' . $GLOBALS ['padLog'] . '-' . uniqid();

    if ( isset ( $GLOBALS ['padDumpToDirDone'] ) ) {

      if ( $dir !== $GLOBALS ['padDumpToDirDone'] )
        padDumpToDirDone ( $info, $dir, $GLOBALS ['padDumpToDirDone'] );

      return $GLOBALS ['padDumpToDirDone'];

    }

    $GLOBALS ['padDumpToDirDone'] = $dir;

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpToDirGo ( $info );

    } catch (Throwable $e) {

      padDumpToDirCatch ( $info, $e, $dir );

    }

    restore_error_handler (); 

    return $dir;

  }


  function padDumpToDirDone ( $info, $dir, $done ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padFilePut ( "$dir/error.txt", "$info\n\n$done" );   
    
    } catch (Throwable $e ) {

      // Ignore errors
      
    }

    restore_error_handler ();

  }  


  function padDumpToDirGo ( $info ) {

    if ( $info ) {
      ob_start (); 
      padDumpInfo ( $info );          
      padDumpFile ( '_ERROR', ob_get_clean () );
    }

    ob_start (); padDumpStack     ();                 padDumpFile ( 'stack',     ob_get_clean () );
    ob_start (); padDumpBuffer    ();                 padDumpFile ( 'buffer',    ob_get_clean () );
    ob_start (); padDumpRequest   ();                 padDumpFile ( 'request',   ob_get_clean () );
    ob_start (); padDumpSQL       ();                 padDumpFile ( 'sql',       ob_get_clean () );
    ob_start (); padDumpHeaders   ();                 padDumpFile ( 'headers',   ob_get_clean () );
    ob_start (); padDumpPhpInfo   ();                 padDumpFile ( 'php-info',  ob_get_clean () );
    ob_start (); padDumpLevel     ();                 padDumpFile ( 'level',     ob_get_clean () );
    ob_start (); padDumpFiles     ();                 padDumpFile ( 'files',     ob_get_clean () );
    ob_start (); padDumpFunctions ();                 padDumpFile ( 'functions', ob_get_clean () );
    ob_start (); padDumpApp       ();                 padDumpFile ( 'app-vars',  ob_get_clean () );
  
    padDumpFields ( $php, $lvl, $cfg, $pad, $ids, $inf, $pq );

    ob_start (); padDumpLines     ( "ID's", $ids );   padDumpFile ( 'ids',       ob_get_clean () );
    ob_start (); padDumpCurl      ( $pad );           padDumpFile ( 'last-curl', ob_get_clean () );
    ob_start (); padDumpXXX       ( $pad, 'pq' ); padDumpFile ( 'sequence',  ob_get_clean () );
    ob_start (); padDumpLines     ( "Info", $inf );   padDumpFile ( 'info',      ob_get_clean () );
    ob_start (); padDumpLines     ( "Level", $lvl );  padDumpFile ( 'lvl-vars',  ob_get_clean () );
    ob_start (); padDumpLines     ( 'Config', $cfg ); padDumpFile ( 'config',    ob_get_clean () );
    ob_start (); padDumpLines     ( 'PHP', $php );    padDumpFile ( 'php-vars',  ob_get_clean () );
    ob_start (); padDumpLines     ( "PAD",   $pad );  padDumpFile ( 'pad-vars',  ob_get_clean () );
    ob_start (); padDumpGlobals   ();                 padDumpFile ( 'globals',   ob_get_clean () );

    padDumpInputToFile () ;

  }


  function padDumpToDirCatch ( $info, $e, $dir ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padFilePut ( "$dir/oops.txt", 
                           "$info\n\n" . 
                           $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage() 
                         );
   
    } catch (Throwable $e2) {

      padDumpToDirCatchCatch ( $info, $e, $e2 );
  
    }

    restore_error_handler ();

  }  


  function padDumpToDirCatchCatch ( $info, $e1, $e2 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      gc_collect_cycles();

      padLogError ( 'DIR-CATCH: ' . $info );
      padLogError ( $e1->getFile() . ':' . $e1->getLine()  . ' DIR-CATCH: ' . $e1->getMessage() );
      padLogError ( $e2->getFile() . ':' . $e2->getLine()  . ' DIR-CATCH: ' . $e2->getMessage() );

    } catch (Throwable $e2) {

      // giving up
  
    }

    restore_error_handler ();

  }  


  function padDumpFile ( $file, $txt ) {

    $dir = $GLOBALS ['padDumpToDirDone'];
    $txt = trim ( $txt );

    padFilePut ( "$dir/$file.html", "<pre>$txt</pre>" );

  }


  function padDumpInputToFile () {

    $txt  = trim ( file_get_contents ('php://input') ?? '' );
    $type = padContentType ( $txt );

    if ( $type == 'csv' )
      $type = 'txt';

    padFilePut ( $GLOBALS ['padDumpToDirDone'] . "/input.$type", $txt );

  }


?>