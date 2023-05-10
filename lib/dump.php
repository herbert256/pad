<?php


  function padDumpToFile ( $file, $info='' ) {

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


  function padDumpGo ($info) {

    echo ( "<div align=\"left\"><pre>" );

    padDumpFields    ( $php, $lvl, $app, $cfg, $pad, $ids );
    padDumpInfo      ( $info );
    padDumpErrors    ( $info );
    padDumpStack     ();
    padDumpExeptions ();
    padDumpLines     ( "ID's", $ids );
    padDumpLevel     ();
    padDumpRequest   ();
    padDumpLines     ( "App variables", $app );
    padDumpXXX       ( $pad, 'padSeq' );
    padDumpLines     ( "PAD variables",   $pad );
    padDumpLines     ( "Level variables", $lvl );
    padDumpSQL       ();
    padDumpHeaders   ();
    padDumpLines     ( 'Configuration', $cfg );
    padDumpLines     ( 'PHP', $php );
    padDumpGlobals   ();

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


  function padDumpExeptions ( ) {

    if ( ! isset ( $GLOBALS ['padExceptions'] ) )
      return;

    foreach ( $GLOBALS ['padExceptions'] as $exception )
      padDumpLines ( 'Exception', $exception );

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

    for ( $lvl=$pad; $lvl>=0; $lvl-- )
      padDumpLines (" Level: $lvl", padDumpGetLevel ($lvl) );

    if ( isset ( $GLOBALS ['padData'] ) and is_array ( $GLOBALS ['padData'] ) )
      for ( $lvl=$pad; $lvl>0; $lvl-- )
        if ( isset ($GLOBALS ['padData'][$lvl]) )
          padDumpLines ('Level '.$lvl, $GLOBALS ['padData'][$lvl] );
    
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


  function padDumpClean ( &$array ) {

    foreach ( $array as $key => $value )
      if ( is_array ($value) ) 
         padDumpClean ( $array [$key] );
      elseif ( is_scalar ($value) )
        $array [$key] = padDumpShort ($value);

  }


  function padDumpGlobals ( ) {

    echo ( "\n<b>GLOBALS</b>\n");

    echo htmlentities ( print_r ( $GLOBALS, TRUE ) );
 
  }


  function padDumpLines ( $info, $source ) {

    if ( is_array ($source) and ! count($source) )
      return;

    if ( is_array($source) )
      padDumpClean ($source);

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


  function padDumpShort ($G) {
  
    if ( $G === NULL)
      $G = '';
  
    return substr ( preg_replace('/\s+/', ' ', $G ), 0, 150 );
  
  }  


?>