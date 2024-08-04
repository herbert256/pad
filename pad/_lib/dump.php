<?php


  function padDump ( $error='' ) {

    set_error_handler ( 'padThrow' );

    try {

      padDumpGo ( $error );

    } catch (Throwable $e) {
  
      include pad . 'error/stop.php';
  
    }

    restore_error_handler ();

    padStop ( 500 );

  }


  function padDumpGo ( $info ) {

    if ( ! headers_sent () ) 
      header ( 'HTTP/1.0 500 Internal Server Error' );

    padEmptyBuffers ();

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

    echo ( "<div align=\"left\"><pre>" );

    padDumpInfo      ( $info );
    padDumpStack     ();
    padDumpLevel     ();
    padDumpInput     ();    
    padDumpBuffer    ();
    padDumpRequest   ();
    
    padDumpFields    ( $php, $lvl, $app, $cfg, $pad, $ids, $trc);

    padDumpLines     ( "App variables", $app );
    padDumpXXX       ( $pad, 'padSeq' ); 
    padDumpXXX       ( $pad, 'padBuild' );
    padDumpCurl      ( $pad );
    padDumpLines     ( "PAD variables",   $pad );
    padDumpLines     ( "Trace variables", $trc );
    padDumpLines     ( "Level variables", $lvl );
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


  function padDumpRemote ( $info ) {

    if ( ! isset ( $GLOBALS ['padDumpToDirDone'] ) ) 
      padDumpToDir ( $info );
        
    echo "Error: " . padID ();

  }


  function padDumpInfo ( $info ) {

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
    
    padDumpStackGo ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS) );

    if ( isset ( $GLOBALS ['padErrorException'] ) )
      padDumpStackGo ( $GLOBALS['padErrorException']->getTrace() );

  }


  function padDumpStackGo ( $stack ) {

    foreach ( $stack as $key => $trace ) {

      extract ( $trace );

      $file     = $file     ?? $GLOBALS['padErrorFile'] ?? '???';
      $line     = $line     ?? $GLOBALS['padErrorLine'] ?? '???';
      $function = $function ?? '???';

      echo ( "$file:$line - $function\n");

      unset ($file);
      unset ($line);
      unset ($function);

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
    
    $opt = $GLOBALS ['padOpt'] [$pad] ?? [];
    $tag = $GLOBALS ['padTag'] [$pad] ?? '';
     
    $prm = '';
    if ( isset ( $opt[0]) ) {
      $prm = $opt[0];
      unset ( $opt[0] );
      if ( isset ( $opt[1]) ) 
        unset ( $opt[1] );
    }

    if     ( $tag and $prm ) $tag = '{' . "$tag $prm" . '}';
    elseif ( $tag          ) $tag = '{' . $tag . '}';
    
    return [
      'tag'     => $tag,
      'type'    => $GLOBALS ['padType'] [$pad] ?? '',
      'name'    => $GLOBALS ['padName'] [$pad] ?? '',
      'pair'    => $GLOBALS ['padPair'] [$pad] ?? '',
      'opt'     => $opt,
      'prm'     => $GLOBALS ['padPrm'] [$pad] ?? '',
      'set-lvl' => $GLOBALS ['padSetLvl'] [$pad] ?? '',
      'set-occ' => $GLOBALS ['padSetOcc'] [$pad] ?? '',
      'base'    => padDumpShort ($GLOBALS ['padBase'][$pad]??''),
      'pad'     => padDumpShort ($GLOBALS ['padPad'][$pad]??''),
      'result'  => padDumpShort ($GLOBALS ['padResult'][$pad]??'')
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

    padDumpLines ( 'Input', file_get_contents('php://input') );

  }

  function padDumpBuffer ( ) {

    padDumpLines ( 'Output buffer', $GLOBALS ['padBuffer'] );

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


  function padDumpFields ( &$php, &$lvl, &$app, &$cfg, &$pad, &$ids, &$trc ) {
    
    $php = $lvl = $app = $cfg = $pad = $ids = $exc = $trc = [];

    $chk1 = [ '_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];

    $chk3 = [ 'padPage','padSesID','padReqID','padRefID','PHPSESSID' ];

    $chk4 = [ 'padOptionsEnd','padOptionsStart','padLevelVars' ];
    
    $settings = file_get_contents ( pad . 'config/config.php' );

    foreach ($GLOBALS as $key => $value)

      if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t"))

        $cfg  [$key] = $value;

      elseif ( substr($key, 0, 6)  == 'padXml' )

        $trc [$key] = $value;

      elseif ( substr($key, 0, 7)  == 'padXref' )

        $trc [$key] = $value;

      elseif ( substr($key, 0, 8)  == 'padTrace' )

        $trc [$key] = $value;

      elseif ( $key == 'padSqlConnect' )
        
        $ignored [$key] = $value;

      elseif ( $key == 'padPadSqlConnect' )
        
        $ignored [$key] = $value;

      elseif ( in_array ( $key, $chk3 ) )
        
        $ids [$key] = $value;

      elseif ( in_array ( $key, $chk4 ) )
        
        $cfg [$key] = $value;

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

  function padDumpToDir ( $info='', $dir='' ) {
 
    if ( ! $dir )
      $dir = "dumps/" . $GLOBALS ['padPage'] . '/' . $GLOBALS ['padLog'];

    if ( isset ( $GLOBALS ['padDumpToDirDone'] ) )
      return padDumpToDirDone ( $info, $dir );

    $GLOBALS ['padDumpToDirDone'] = $dir;

    set_error_handler ( 'padThrow' );

    try {

      padDumpToDirGo ( $info );

    } catch (Throwable $e) {

      padDumpToDirCatch ( $info, $e, $dir );

    }

    restore_error_handler (); 

    return $dir;

  }


  function padDumpToDirDone ( $info, $dir ) {

    if ( $dir !== $GLOBALS ['padDumpToDirDone'] )
      padDumpToDirDoneGo ( $info, $dir, $GLOBALS ['padDumpToDirDone'] );

    return $GLOBALS ['padDumpToDirDone'];

  }


  function padDumpToDirDoneGo ( $info, $dir, $done ) {

    set_error_handler ( 'padThrow' );

    try {

      padFilePutContents ( "$dir/error.txt", padData . "$info\n\n$done" );   
    
    } catch (Throwable $e ) {

      // Ignore errors
      
    }

    restore_error_handler ();

  }  


  function padDumpToDirGo ( $info ) {

    ob_start (); padDumpInfo      ( $info );          padDumpFile ( '_ERROR',    ob_get_clean () );
    ob_start (); padDumpStack     ();                 padDumpFile ( 'stack',     ob_get_clean () );
    ob_start (); padDumpBuffer    ();                 padDumpFile ( 'buffer',    ob_get_clean () );
    ob_start (); padDumpRequest   ();                 padDumpFile ( 'request',   ob_get_clean () );
    ob_start (); padDumpSQL       ();                 padDumpFile ( 'sql',       ob_get_clean () );
    ob_start (); padDumpHeaders   ();                 padDumpFile ( 'headers',   ob_get_clean () );
    ob_start (); padDumpPhpInfo   ();                 padDumpFile ( 'php-info',  ob_get_clean () );
    ob_start (); padDumpLevel     ();                 padDumpFile ( 'level',     ob_get_clean () );
    ob_start (); padDumpFiles     ();                 padDumpFile ( 'files',     ob_get_clean () );
    ob_start (); padDumpFunctions ();                 padDumpFile ( 'functions', ob_get_clean () );
  
    padDumpFields ( $php, $lvl, $app, $cfg, $pad, $ids, $trc );

    ob_start (); padDumpLines     ( "ID's", $ids );   padDumpFile ( 'ids',       ob_get_clean () );
    ob_start (); padDumpLines     ( "App", $app );    padDumpFile ( 'app-vars',  ob_get_clean () );
    ob_start (); padDumpCurl      ( $pad );           padDumpFile ( 'last-curl', ob_get_clean () );
    ob_start (); padDumpXXX       ( $pad, 'padSeq' ); padDumpFile ( 'sequence',  ob_get_clean () );
    ob_start (); padDumpLines     ( "Trace", $trc );  padDumpFile ( 'trace',     ob_get_clean () );
    ob_start (); padDumpLines     ( "Level", $lvl );  padDumpFile ( 'lvl-vars',  ob_get_clean () );
    ob_start (); padDumpLines     ( 'Config', $cfg ); padDumpFile ( 'config',    ob_get_clean () );
    ob_start (); padDumpLines     ( 'PHP', $php );    padDumpFile ( 'php-vars',  ob_get_clean () );
    ob_start (); padDumpGlobals   ();                 padDumpFile ( 'globals',   ob_get_clean () );
    ob_start (); padDumpLines     ( "PAD",   $pad );  padDumpFile ( 'pad-vars',  ob_get_clean () );

    padDumpInputToFile () ;

  }


  function padDumpToDirCatch ( $info, $e, $dir ) {

    set_error_handler ( 'padThrow' );

    try {

      padFilePutContents ( "$dir/oops.txt", 
                           "$info\n\n" . 
                           $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage() 
                         );
   
    } catch (Throwable $e2) {

      padDumpToDirCatchCatch ( $info, $e, $e2 );
  
    }

    restore_error_handler ();

  }  


  function padDumpToDirCatchCatch ( $info, $e1, $e2 ) {

    set_error_handler ( 'padThrow' );

    try {

      gc_collect_cycles();

      error_log ( 'DIR-CATCH: ' . $info, 4 );
      error_log ( $e1->getFile() . ':' . $e1->getLine()  . ' DIR-CATCH: ' . $e1->getMessage(), 4 );
      error_log ( $e2->getFile() . ':' . $e2->getLine()  . ' DIR-CATCH: ' . $e2->getMessage(), 4 );

    } catch (Throwable $e2) {

      // giving up
  
    }

    restore_error_handler ();

  }  


  function padDumpFile ( $file, $txt ) {

    $dir = $GLOBALS ['padDumpToDirDone'];
    $txt = trim ( $txt );

    padDumpWrite ( "$dir/$file.html", "<pre>$txt</pre>" );

  }


  function padDumpInputToFile () {

    $txt  = trim ( file_get_contents('php://input') ?? '' );
    $type = padContentType ( $txt );

    if ( $type == 'csv' )
      $type = 'txt';

    padDumpWrite ( $GLOBALS ['padDumpToDirDone'] . "/input.$type", $txt );

  }


  function padDumpWrite ( $file, $data ) {

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);

    if ( ! $data )
      return;

    $file = padData . $file;

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! file_exists ($dir) )
      mkdir ($dir, $GLOBALS ['padDirMode'], true );
      
    if ( ! file_exists ($file) ) {      
      touch ($file);
      chmod ($file, $GLOBALS ['padFileMode']);
    }
      
    file_put_contents ( $file, $data );
    
  }


?>