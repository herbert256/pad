<?php


  function padDump ( $info='' ) {

    set_error_handler     ( 'padDumpError' );
    set_exception_handler ( 'padDumpException' );

    try {

      $GLOBALS ['padInDump'] = TRUE;

      $GLOBALS ['padErrrorList'] [] = $info;

      if ( ! headers_sent () ) 
        header ( 'HTTP/1.0 500 Internal Server Error' );

      padCloseHtml ();

      padEmptyBuffers ();

      if ( padLocal () ) {

        echo ( "<div align=\"left\"><pre>" );

        padDumpFields    ( $php, $lvl, $app, $cfg, $pad, $ids, $exc );
        padDumpInfo      ( $info );
        padDumpErrors    ( $info );
        padDumpHistShort ();
        padDumpStack     ();
        padDumpLevel     ();
        padDumpLines     ( "App variables", $app );
        padDumpExeptions ( $exc );
        padDumpRequest   ();
        padDumpXXX       ( $pad, 'padSeq' );
        padDumpXXX       ( $pad, 'padBuild' );
        padDumpLines     ( "PAD variables",   $pad );
        padDumpLines     ( "Level variables", $lvl );
        padDumpLines     ( "ID's", $ids );
        padDumpSQL       ();
        padDumpHeaders   ();
        padDumpHistory   ();
        padDumpLines     ( 'Configuration', $cfg );
        padDumpLines     ( 'PHP', $php );
        padDumpFiles     ();
        padDumpFunctions ();
        padDumpGlobals   ();

        echo ( "</pre></div>" );

      } else {

        padErrorLog ( "DUMP: $info" );
        echo "Error: " . padID ();

      }
       
      $GLOBALS ['padSent']   = TRUE;
      $GLOBALS ['padOutput'] = '';

      padStop (500);

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


  function padDumpProblem ( $error, $file, $line) {

    padDumpCleanErrors ();

    $org = $error;

    if ( isset ( $GLOBALS ['padErrrorList'] ) )
      foreach ( $GLOBALS ['padErrrorList'] as $list )
        if ( $list <> $org )
          $error .= "\n" . $list;

    padBootStop ( $error, $file, $line );

  }


  function padDumpHistShort () {

    if ( ! isset ( $GLOBALS ['padHistory'] ) )
      return;

    if ( count ( $GLOBALS ['padHistory'] ) > 10 )
      padDumpLines ( "History", array_slice ( $GLOBALS ['padHistory'], -10 ) );
    else  
      padDumpLines ( "History", $GLOBALS ['padHistory'] );

  }


  function padDumpHistory () {

    if ( ! isset ( $GLOBALS ['padHistory'] ) )
      return;

    padDumpLines ( "History", $GLOBALS ['padHistory'] );

  }


  function padDumpInfo ( $info ) {

    if ( isset ( $GLOBALS ['padErrrorList'] ) )
      if ( $info and in_array ( $info, $GLOBALS ['padErrrorList'] ) )
        return;

    if ( trim($info) )
      echo ( "<hr><b>" . htmlentities($info) . "</b><hr><br>" ); 

  } 


  function padDumpErrors ($info) {

    if ( ! isset ( $GLOBALS ['padErrrorList'] ) )
      return;

    padDumpCleanErrors ($info);

    if ( count ( $GLOBALS ['padErrrorList'] ) == 1 ) 

      foreach ( $GLOBALS ['padErrrorList'] as $error ) 
        echo ( "<hr><b>" . htmlentities($error) . "</b><hr><br>" ); 

    elseif ( count ( $GLOBALS ['padErrrorList'] ) > 1 ) {

      echo ( "<b>Errors</b>\n");

      $errors = array_reverse ( $GLOBALS ['padErrrorList'] );

      foreach ( $errors as $error )
        echo ( "    " . htmlentities($error) . "\n" );

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

    echo "<br>";
    
    padDumpStackGo ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS), '' );

    if ( isset ( $GLOBALS ['padExceptions'] ) )
      foreach ( $GLOBALS ['padExceptions'] as $exception )
        padDumpStackGo ( $exception->getTrace(), ' - exception' );

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

    if ($tag)
      $tag = '{' . "$tag $prm" . '}';

    global $padHtml, $padStart, $padEnd;

    $before = substr ( $padHtml [$pad], 0, $padStart [$pad] );
    $after  = substr ( $padHtml [$pad],    $padEnd [$pad]+1 );

    if ( strlen($before) > 100)
      $before = substr($before, -100);

    if ( strlen($after) > 100)
      $after = substr($after, 0, 100);

    return [
      'tag'    => $tag,
      'type'   => $GLOBALS ['padType'] [$pad] ?? '',
      'name' => $GLOBALS ['padName'] [$pad] ?? '',
      'pair'   => $GLOBALS ['padPair'] [$pad] ?? '',
      'opt'    => $opt,
      'prm'    => $GLOBALS ['padPrm'] [$pad] ?? '',
      'set-lvl' => $GLOBALS ['padSetLvl'] [$pad] ?? '',
      'set-occ' => $GLOBALS ['padSetOcc'] [$pad] ?? '',
      'true' => padDumpShort ($GLOBALS ['padTrue'][$pad]??''),
      'false' => padDumpShort ($GLOBALS ['padFalse'][$pad]??''),
      'base' => padDumpShort ($GLOBALS ['padBase'][$pad]??''),
      'html' => padDumpShort ($GLOBALS ['padHtml'][$pad]??''),
      'result' => padDumpShort ($GLOBALS ['padResult'][$pad]??''),
      'before'    => $before,
      'after'    => $after
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
      $G = ' !!! array as input for padDumpShort !!!';
  
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

    $id = $GLOBALS ['padPage'] . '/' . $GLOBALS ['padReqID'] . '-' . padRandomString();

    try {

      padDumpFields ( $php, $lvl, $app, $cfg, $pad, $ids, $exc );

      ob_start (); padDumpInfo      ( $info );          padDumpFile ( 'info',        ob_get_clean (), $dir );
      ob_start (); padDumpErrors    ( $info );          padDumpFile ( 'errors',      ob_get_clean (), $dir );
      ob_start (); padDumpStack     ();                 padDumpFile ( 'stack',       ob_get_clean (), $dir );
      ob_start (); padDumpExeptions ( $exc );           padDumpFile ( 'exception',   ob_get_clean (), $dir );
      ob_start (); padDumpLines     ( "ID's", $ids );   padDumpFile ( 'ids',         ob_get_clean (), $dir );
      ob_start (); padDumpLevel     ();                 padDumpFile ( 'level',       ob_get_clean (), $dir );
      ob_start (); padDumpRequest   ();                 padDumpFile ( 'request',     ob_get_clean (), $dir );
      ob_start (); padDumpLines     ( "App", $app );    padDumpFile ( 'app-vars',    ob_get_clean (), $dir );
      ob_start (); padDumpXXX       ( $pad, 'padSeq' ); padDumpFile ( 'sequence',    ob_get_clean (), $dir );
      ob_start (); padDumpFiles     ();                 padDumpFile ( 'files',       ob_get_clean (), $dir );
      ob_start (); padDumpFunctions ();                 padDumpFile ( 'functions',   ob_get_clean (), $dir );
      ob_start (); padDumpLines     ( "PAD",   $pad );  padDumpFile ( 'pad-vars',    ob_get_clean (), $dir );
      ob_start (); padDumpLines     ( "Level", $lvl );  padDumpFile ( 'level-vars',  ob_get_clean (), $dir );
      ob_start (); padDumpSQL       ();                 padDumpFile ( 'sql',         ob_get_clean (), $dir );
      ob_start (); padDumpHeaders   ();                 padDumpFile ( 'headers',     ob_get_clean (), $dir );
      ob_start (); padDumpLines     ( 'Config', $cfg ); padDumpFile ( 'config',      ob_get_clean (), $dir );
      ob_start (); padDumpLines     ( 'PHP', $php );    padDumpFile ( 'php-vars',    ob_get_clean (), $dir );
      ob_start (); padDumpPhpInfo   ();                 padDumpFile ( 'php-info',    ob_get_clean (), $dir );
      ob_start (); padDumpGlobals   ();                 padDumpFile ( 'globals',     ob_get_clean (), $dir );
      ob_start (); padDumpHistory   ();                 padDumpFile ( 'history',     ob_get_clean (), $dir );

    } catch (Throwable $e) {

      padFilePutContents ( "dumps/$id/oops.txt", "$info\n\n" . $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage() );
  
    }

    restore_error_handler ();
    error_reporting ( $errorReporting );

  }


  function padDumpFile ( $file, $txt, $dir ) {

    if ( ! trim ( $txt ) )
      return;

    padFilePutContents ( "$dir/$file.html", "<pre>$txt</pre>" );

  }


  function padDumpFields ( &$php, &$lvl, &$app, &$cfg, &$pad, &$ids, &$exc ) {

    $php = $lvl = $app = $cfg = $pad = $ids = $exc = [];

    $chk1 = [ '_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];

    $chk3 = [ 'padPage','padSesID','padReqID','padRefID','PHPSESSID' ];

    $chk4 = [ 'padOptionsEnd','padOptionsStart','padLevelVars' ];

    $settings = padFileGetContents(pad . 'config/config.php');

    foreach ($GLOBALS as $key => $value)

      if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t"))

        $cfg  [$key] = $value;

      elseif ( $key == 'padExceptions' )
        
        $exc [$key] = $value;

      elseif ( $key == 'padSqlConnect' )
        
        $ignored [$key] = $value;

      elseif ( $key == 'padHistory' )
        
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


?>