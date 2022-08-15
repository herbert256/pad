<?php


  function pDump ($info='') {

    if ( ! pLocal () )
      return FALSE ;

    pEmpty_buffers ();
    gc_collect_cycles ();
    pClose_html    ();
    pDump_vars     ($info);

    $GLOBALS ['padSent'] = TRUE;

    pStop (500);

  }   


  function pDump_to_file ($file, $info='') {

    pFile_put_contents ( $file, pDump_get ($info) );
        
  }


  function pDump_get ($info='') {

    ob_start();

    pDump_vars ($info);

    return ob_get_clean();
        
  }


  function pDump_vars ($info='') {

    pTraceFields ( $php, $lvl, $app, $cfg, $pad, $ids);

    echo ("<div align=\"left\"><pre>");

    if ($info)
      echo ("<hr><b>$info</b><hr><br>");

    pDumpSequence ($pad);
    pDumpEval     ();
    pDumpStack    ();
    pDumpLevel    ();
    pDumpRequest  ();
    pDumpArray    ( "APP variables", $app);
    pDumpArray    ( "PAD variables", $pad);
    pDumpArray    ( "Level variables", $lvl);
    pDumpSQL      ();
    pDumpHeaders  ();
    pDumpArray    ( "ID's", $ids);
    pDumpArray    ('Configuration', $cfg );
    pDumpArray    ('PHP', $php);

    echo ( "\n<b>GLOBALS</b>\n" );
    echo htmlentities ( print_r ( $GLOBALS, TRUE ) );

    echo ( "\n</pre></div>" );

  }


  function pDumpSequence ($pad) {

    if ( ! isset($GLOBALS ['padIn_sequence'] ) or $GLOBALS ['padIn_sequence'] === FALSE ) 
      return;

    $seq = [];
    
    foreach ( $pad as $key )
      if ( substr($key, 0, 7) == 'padSeq') {
        unset ($pad[$key]);
        $seq [] = $key;
      }   

    pDumpArray("Sequence variables", $seq);

    echo ( "\n\n");

  }


  function pDumpEval () {

    if ( ! isset($GLOBALS ['padTrace_stage'] ) or $GLOBALS ['padTrace_stage'] == 'end' )
      return;

    echo ( "<b>Eval details</b>\n");

    pDump_field  ( 'eval',   $GLOBALS ['padTrace_eval']      );
    pDump_field  ( 'myself', $GLOBALS ['padTrace_myself']    );

    if ( count ( $GLOBALS ['padTrace_now'] ) )
      pDumpArray ( 'now', $GLOBALS ['padTrace_now']);

    pDumpArray ( 'parsed', $GLOBALS ['padTrace_parsed']);
    pDumpArray ( 'after',  $GLOBALS ['padTrace_after']);
    pDumpArray ( 'go',     $GLOBALS ['padTrace_go']);
    pDumpArray ( 'result', $GLOBALS ['padEval_result'] );

    echo ( "\n\n");

  }


  function pDumpStack () {

    $padDebug_backtrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);

    echo ( "<b>Stack</b>\n");
    
    foreach ( $padDebug_backtrace as $key => $trace ) {
      extract ( $trace );
      echo ( "    $file:$line - $function\n");
    }
    
  }


  function pDumpSQL () {

    if ( isset ( $GLOBALS ['padSql_connect'     ] ) ) 
      pDump_object ('MySQL-App', $GLOBALS ['padSql_connect']      );
    
    if ( isset ( $GLOBALS ['padPad_sql_connect' ] ) ) 
      pDump_object ('MySQL-PAD', $GLOBALS ['padPad_sql_connect']  );

  }


  function pDumpHeaders () {

                                           pDumpArray ('Headers-in',  getallheaders());
    if ( isset ( $GLOBALS ['padHeaders'] ) ) pDumpArray ('Headers-out', $GLOBALS ['padHeaders'] );

  }


  function pDumpRequest () {

    if ( isset ( $_REQUEST ) and count ( $_REQUEST ) )
      pDumpArray ('Request variables', $_REQUEST);

  }


  function pDumpLevel () {

    if ( ! isset ( $GLOBALS['pad'] ) or $GLOBALS['pad'] < 0 )
      return;

    for ( $lvl=$GLOBALS['pad']; $lvl>=0; $lvl-- )
      pDumpArray (" Level: $lvl", pTraceGetLevel ($lvl) );

    if ( isset ( $GLOBALS ['padData'] ) and is_array ( $GLOBALS ['padData'] ) )
      for ( $lvl=p(); $lvl>=0; $lvl-- )
        if ( isset ($GLOBALS ['padData'][$lvl]) )
          pDumpArray ('Level '.$lvl, $GLOBALS ['padData'][$lvl] );
    
  }


  function pDump_field ($field, $value) {
    echo ( "\n  [$field] => " . htmlentities(pDump_short($value??''))); 
  }


  function pDump_short ($G) {
    if ( $G === NULL)
      $G = '';
    return substr ( preg_replace('/\s+/', ' ', $G ), 0, 100 );
  }  


  function pDump_sanitize ($array) {

    foreach ($array as $key => $val)
      if ( $key == 'GLOBALS' )
        $array [$key] = '*** GLOBALS ***';
      elseif ( is_array ($val) )
        $array [$key] = pDump_sanitize ($val);
      elseif ( is_object($val) )
        $array [$key] = '***object***';
      elseif ( is_resource($val) )
        $array [$key] = '***resource***';
      else
        $array [$key] = pDump_short ( $val );

    return $array;

  }


  function pDumpArray ( $txt, $arr ) {

    if ( ! is_array($arr) ) {
      echo ( "\n  [$txt] => [todo, not array] \n");
      return;
    }

    echo ( "\n<b>$txt</b>");

    if ( ! count ($arr )) {
      echo ( "\n  []\n");
      return;
    }

    $arr = pDump_sanitize ($arr);

    $pad = htmlentities ( print_r ( $arr, TRUE ) ) ;

    $pad = str_replace(" =&gt; Array\n" ,"\n", $pad);
    $pad = str_replace(")\n\n" ,")\n", $pad);
    $pad = preg_replace("/[\n]\s+\(/", "", $pad);
    $pad = preg_replace("/[\n]\s+\)/", "", $pad);
    $pad = str_replace("&lt;/address&gt;\n", "&lt;/address&gt;", $pad);

    echo ( "\n" . substr($pad, 8, strlen($pad) - 10));

  }


  function pDump_object ( $txt, $arr) {

    $pad = htmlentities ( print_r ( $arr, TRUE ) ) ;
    $pad = preg_replace("/[\n]\(/", "", $pad);
    $pad = preg_replace("/[\n]\\)/", "", $pad);
    $pad = substr($pad, 0,-1);

    echo ( "\n  [$txt] $pad");

  }


?>