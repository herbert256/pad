<?php


  function padDump ($info='') {

    if ( ! padLocal () )
      return FALSE ;

    padEmptyBuffers ();
    gc_collect_cycles ();
    padCloseHtml    ();
    padDumpGo       ($info);

    $GLOBALS ['padSent'] = TRUE;

    padStop (500);

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

    echo ( "<div align=\"left\"><pre>" );

    if ($info)
      echo ( "<hr><b>$info</b><hr><br>" );

    padTraceFields  ( $php, $lvl, $app, $cfg, $pad, $ids );

    padDumpErrors   ();
    padDumpStack    ();
    padDumpLevel    ();
    padDumpRequest  ();
    padDumpArray    ( "APP variables", $app );
    padDumpXXX      ( $pad, 'padSeq' );
    padDumpXXX      ( $pad, 'padEval' );
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


  function padDumpErrors () {

    if ( ! isset ( $GLOBALS ['padErrrorList'] ) )
      return;

    if ( count ( $GLOBALS ['padErrrorList'] ) < 2 )
      return;

    padDumpArray ( 'Errors', $GLOBALS ['padErrrorList'] );

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

    if ( ! isset ( $GLOBALS['pad'] ) or $GLOBALS['pad'] < 0 )
      return;

    for ( $lvl=$GLOBALS['pad']; $lvl>=0; $lvl-- )
      padDumpArray (" Level: $lvl", padTraceGetLevel ($lvl) );

    if ( isset ( $GLOBALS ['padData'] ) and is_array ( $GLOBALS ['padData'] ) )
      for ( $lvl=p(); $lvl>=0; $lvl-- )
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