<?php


  function padDump ($info='') {

    if ( ! padLocal () )
      return FALSE ;

    padEmptyBuffers ();
    gc_collect_cycles ();
    padCloseHtml    ();
    padDumpVars     ($info);

    $GLOBALS ['padSent'] = TRUE;

    padStop (500);

  }   


  function padDumpToFile ($file, $info='') {

    padFilePutContents ( $file, padDumpGet ($info) );
        
  }


  function padDumpGet ($info='') {

    ob_start();

    padDumpVars ($info);

    return ob_get_clean();
        
  }


  function padDumpVars ($info='') {

    padTraceFields ( $php, $lvl, $app, $cfg, $pad, $ids);

    echo ("<div align=\"left\"><pre>");

    if ($info)
      echo ("<hr><b>$info</b><hr><br>");

    padDumpSequence ($pad);
    padDumpadEval     ();
    padDumpStack    ();
    padDumpLevel    ();
    padDumpRequest  ();
    padDumpArray    ( "APP variables", $app);
    padDumpArray    ( "PAD variables", $pad);
    padDumpArray    ( "Level variables", $lvl);
    padDumpSQL      ();
    padDumpadHeaders  ();
    padDumpArray    ( "ID's", $ids);
    padDumpArray    ('Configuration', $cfg );
    padDumpArray    ('PHP', $php);

    echo ( "\n<b>GLOBALS</b>\n" );
    echo htmlentities ( print_r ( $GLOBALS, TRUE ) );

    echo ( "\n</pre></div>" );

  }


  function padDumpSequence ($pad) {

    if ( ! isset($GLOBALS ['padInSequence'] ) or $GLOBALS ['padInSequence'] === FALSE ) 
      return;

    $seq = [];
    
    // foreach ( $pad as $key )
    //   if ( substr($key, 0, 7) == 'padSeq') {
    //     unset ($pad[$key]);
    //     $seq [] = $key;
    //   }   

    padDumpArray("Sequence variables", $seq);

    echo ( "\n\n");

  }


  function padDumpadEval () {

    if ( ! isset($GLOBALS ['padTrace_stage'] ) or $GLOBALS ['padTrace_stage'] == 'end' )
      return;

    echo ( "<b>Eval details</b>\n");

    padDumpField  ( 'eval',   $GLOBALS ['padTrace_eval']      );
    padDumpField  ( 'myself', $GLOBALS ['padTrace_myself']    );

    if ( count ( $GLOBALS ['padTrace_now'] ) )
      padDumpArray ( 'now', $GLOBALS ['padTrace_now']);

    padDumpArray ( 'parsed', $GLOBALS ['padTrace_parsed']);
    padDumpArray ( 'after',  $GLOBALS ['padTrace_after']);
    padDumpArray ( 'go',     $GLOBALS ['padTrace_go']);
    padDumpArray ( 'result', $GLOBALS ['padEvalResult'] );

    echo ( "\n\n");

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


  function padDumpadHeaders () {

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
        $array [$key] = '***object***';
      elseif ( is_resource($val) )
        $array [$key] = '***resource***';
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
      echo ( "\n  []\n");
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