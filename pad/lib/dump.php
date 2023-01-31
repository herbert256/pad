<?php


  function padDumpFromApp ($info='') {

    if ( ! padLocal () ) 
      return padError ("Dump not allowed: $info");

    padDumpTry ($info);

  } 


  function padDump ($info='') {

    $GLOBALS ['padDump'] = $info;

    set_error_handler     ( 'padDumpError'     );
    set_exception_handler ( 'padDumpException' );

    try {
      padDumpTry ($info);
    } catch (Throwable $error) {
      padDumpCatch ($error);
    }

  }   


  function padDumpTry ($info) {

    padEmptyBuffers ();

    if ( ! headers_sent () )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    if ( padLocal () ) {

      padCloseHtml ();
      padDumpGo    ($info);

    } else {

      padErrorLog ( "DUMP: $info" );
      padErrorID ();

    }
     
    flush();
     
    $GLOBALS ['padSent']   = TRUE;
    $GLOBALS ['padOutput'] = '';

    padStop (500);

  }  

  function padDumpError ( $type, $error, $file, $line ) {
    padDumpProblem ( 'DUMP-ERROR: ' . $error , $file, $line );
  }

  function padDumpException ( $error ) {
    padDumpProblem ( 'DUMP-EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
  }

  function padDumpCatch ( $error ) {
    padDumpProblem ( 'DUMP-CATCH: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
  }

  function padDumpProblem ( $error, $file, $line) {

    gc_collect_cycles ();
    unset ( $GLOBALS ['padOutput'] );
    unset ( $GLOBALS ['padBase']   );
    unset ( $GLOBALS ['padHtml']   );
    unset ( $GLOBALS ['padResult'] );
    gc_collect_cycles ();

    if ( $GLOBALS ['padDump']  ) $error .= ' ||| ' . $GLOBALS ['padDump'];
    if ( $GLOBALS ['padError'] ) $error .= ' ||| ' . $GLOBALS ['padError'];

    padBootStop ( $error, $file, $line );

  }


  function padDumpToFile ($file, $info='') {

    padFilePutContents ( $file, padDumpGet ($info) );
        
  }


  function padDumpGet ($info='') {

    ob_start();

    padDumpGo ($info);

    return ob_get_clean();
        
  }


  function padDumpGo ($info='') {

    padTraceFields  ( $php, $lvl, $app, $cfg, $pad, $ids );

    echo ( "<div align=\"left\"><pre>" );

    padDumpInfo     ( $info );
    padDumpErrors   ();
    padDumpSource   ();
    padDumpStack    ();
    padDumpLevel    ();
    padDumpRequest  ();
    padDumpArray    ( "APP variables", $app );
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

  function padDumpSource (  ) {

    global $pad, $page;

    #echo padInclude ( 'pad', 'example/example', "&exampleApp=$app&examplePage=$page&noResult=1"); 

  }

  function padDumpInfo ( $info ) {

    if ( ! $info )
      return;

    if ( isset ( $GLOBALS ['padErrrorList'] ) and count ( $GLOBALS ['padErrrorList'] ) > 1 )
      foreach ( $GLOBALS ['padErrrorList'] as $error )
        if ( padMakeSafe($info) == padMakeSafe($error) )
          return;

    echo ( "<hr><b>$info</b><hr><br>" ); 

  } 


  function padDumpErrors () {

    if ( ! isset ( $GLOBALS ['padErrrorList'] ) )
      return;

    if ( count ( $GLOBALS ['padErrrorList'] ) < 2 )
      return;

    echo ( "<b>Errors</b>\n");

    $errors = array_reverse ( $GLOBALS ['padErrrorList'] );

    foreach ( $errors as $error )
      echo ( "    $error\n" );

    echo ( "\n" );

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
      padDumpObject ('MySQL-PAD', $GLOBALS ['padPadSqlConnect']  );

  }


  function padDumpHeaders () {

                                             padDumpArray ('Headers-in',  getallheaders());
    if ( isset ( $GLOBALS ['padHeaders'] ) ) padDumpArray ('Headers-out', $GLOBALS ['padHeaders'] );

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
      padDumpArray (" Level: $lvl", padTraceGetLevel ($lvl) );

    if ( isset ( $GLOBALS ['padData'] ) and is_array ( $GLOBALS ['padData'] ) )
      for ( $lvl=$pad; $lvl>=0; $lvl-- )
        if ( isset ($GLOBALS ['padData'][$lvl]) )
          padDumpArray ('Level '.$lvl, $GLOBALS ['padData'][$lvl] );
    
  }

  function padDumpGlobals () {

    echo ( "\n<b>GLOBALS</b>\n" );

    echo htmlentities ( print_r ( $GLOBALS, TRUE ) );

  }


  function padDumpField ($field, $value) {

    echo ( "\n  [$field] => " . htmlentities(padDumpShort($value??''))); 
  
  }


  function padDumpShort ($G) {
  
    if ( $G === NULL)
  
      $G = '';
  
    return substr ( preg_replace('/\s+/', ' ', $G ), 0, 100 );
  
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


  function padDumpObject ( $txt, $arr) {

    $pad = htmlentities ( print_r ( $arr, TRUE ) ) ;
    $pad = preg_replace("/[\n]\(/", "", $pad);
    $pad = preg_replace("/[\n]\\)/", "", $pad);
    $pad = substr($pad, 0,-1);

    echo ( "\n  [$txt] $pad");

  }


?>