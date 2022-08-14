<?php


  function pDump ($info='') {

    if ( ! pLocal () )
      return FALSE ;

    pEmpty_buffers ();
    gc_collect_cycles ();
    pClose_html    ();
    pDump_vars     ($info);

    $GLOBALS ['pSent'] = TRUE;

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

    pFields ( $php, $lvl, $app, $cfg, $pad, $ids);

    echo ("<div align=\"left\"><pre>");

    if ($info)
      echo ("<hr><b>$info</b><hr><br>");

    pDumpSequence ();
    pDumpEval     ();
    pDumpStack    ();
    pDumpArray    ( "ID's", $ids);
    pDumpLevel    ();
    pDumpRequest  ();
    pDumpArray    ( "APP variables", $app);
    pDumpArray    ( "PAD variables", $pad);
    pDumpArray    ( "Level variables", $lvl);
    pDumpSQL      ();
    pDumpHeaders  ();
    pDumpArray    ('Configuration', $cfg );
    pDumpArray    ('PHP', $php);

    echo ( "\n<b>GLOBALS</b>\n" );
    echo htmlentities ( print_r ( $GLOBALS, TRUE ) );

    echo ( "\n</pre></div>" );

  }


  function pFields ( &$php, &$lvl, &$app, &$cfg, &$pad, &$ids ) {

    $php = $lvl = $app = $cfg = $pad = $ids = [];

    $chk3 = ['page','app','PADSESSID','PADREQID','PHPSESSID','PADREFID' ];

    $not  = [ 'pTag','pType','pPair','pTrue','pFalse','pPrm','pPrms','pPrmsType','pPrmsTag','pPrmsVal','pName','pData','pCurrent','pKey','pDefault','pWalk','pWalkData','pDone','pOccur','pStart','pEnd','pBase','pHtml','pResult','pHit','pNull','pElse','pArray','pText','pLevelDir','pOccurDir','pSave_vars','pDelete_vars','pSet_save','pSet_delete','pTagCnt' ];

    $chk1 = ['_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];

    $chk2 = ['pTag','pType','pPair','pPrmsType','pPrm','pPrms','pPrmsTag','pPrmsVal','pTrue','pFalse','pBase','pHtml','pResult','pName','pDefault','pWalk','pHit','pNull','pElse','pArray','pText', 'pData'];

    $settings = pFile_get_contents(PAD . 'config/config.php');

    foreach ($GLOBALS as $key => $value) {

      if ( ! in_array ($key, $not) ) {

        if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t"))

          $cfg  [$key] = $value;

        elseif ( in_array ( $key, $chk3 ) )
          
          $ids [$key] = $value;

        elseif ( in_array ( $key, $chk1 ) )
          
          $php [$key] = $value;

        elseif ( in_array ( $key, $chk2 ) )
          
          $lvl [$key] = $value;
   
        elseif ( substr($key, 0, 1)  == 'p' )

          $pad [$key] = $value;

        else

          $app [$key] = $value;

      }

    }

    ksort($app);
    ksort($cfg);
    ksort($php);
    ksort($lvl);
    ksort($pad);

  }


  function pDumpSequence () {

    if ( ! isset($GLOBALS ['pIn_sequence'] ) or $GLOBALS ['pIn_sequence'] === FALSE ) 
      return;

    $seq = [];
    
    foreach ( $pad as $key )
      if ( substr($key, 0, 7) == 'pSeq') {
        unset ($pad[$key]);
        $seq [] = $key;
      }   

    pDump_fields ($seq, "Sequence variables");

    echo ( "\n\n");

  }


  function pDumpEval () {

    if ( ! isset($GLOBALS ['pTrace_stage'] ) or $GLOBALS ['pTrace_stage'] == 'end' )
      return;

    echo ( "<b>Eval details</b>\n");

    pDump_field  ( 'eval',   $GLOBALS ['pTrace_eval']      );
    pDump_field  ( 'myself', $GLOBALS ['pTrace_myself']    );

    if ( count ( $GLOBALS ['pTrace_now'] ) )
      pDumpArray  ( 'now', $GLOBALS ['pTrace_now']);

    pDumpArray  ( 'parsed', $GLOBALS ['pTrace_parsed']);
    pDumpArray  ( 'after',  $GLOBALS ['pTrace_after']);
    pDumpArray  ( 'go',     $GLOBALS ['pTrace_go']);
    pDumpArray  ( 'result', $GLOBALS ['pEval_result'] );

    echo ( "\n\n");

  }


  function pDumpStack () {

    $pDebug_backtrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);

    echo ( "<b>Stack</b>\n");
    
    foreach ( $pDebug_backtrace as $key => $trace ) {
      extract ( $trace );
      echo ( "    $file:$line - $function\n");
    }
    
  }


  function pDumpSQL () {

    if ( isset ( $GLOBALS ['pSql_connect'     ] ) ) 
      pDump_object ('MySQL-App', $GLOBALS ['pSql_connect']      );
    
    if ( isset ( $GLOBALS ['pPad_sql_connect' ] ) ) 
      pDump_object ('MySQL-PAD', $GLOBALS ['pPad_sql_connect']  );

  }


  function pDumpHeaders () {

                                           pDumpArray  ('Headers-in',  getallheaders());
    if ( isset ( $GLOBALS ['pHeaders'] ) ) pDumpArray  ('Headers-out', $GLOBALS ['pHeaders'] );

  }


  function pDumpRequest () {

    if ( isset ( $_REQUEST ) and count ( $_REQUEST ) )
      pDumpArray  ('Request variables', $_REQUEST);

  }


  function pDumpLevel () {

    if ( ! isset ( $GLOBALS ['p'] ) )
      return;

    if ( $GLOBALS ['p'] < 0 )
      return;

    if ( isset ( $GLOBALS ['p'] ) )
      for ( $p=$GLOBALS ['p'];  $p>=0; $p-- )
        pDumpArray  ("Level: $p", pTraceGetLevel ($p));

    if ( isset ( $GLOBALS ['pData'] ) and is_array ( $GLOBALS ['pData'] ) )
      for ( $lvl=$GLOBALS ['p']; $lvl>=0; $lvl-- )
        if ( isset ($GLOBALS ['pData'][$lvl]) )
          pDumpArray  ('Level '.$lvl, $GLOBALS ['pData'][$lvl] );
    
  }


  function pDump_fields ($fields, $text) {

    echo ( "\n<b>$text</b>");

    foreach ($fields as $key)
      if ( isset($GLOBALS[$key]) )
        if (is_object($GLOBALS[$key]))
          pDump_object ($key, $GLOBALS[$key]);
        elseif (is_array ($GLOBALS[$key]))
          pDumpArray ($key,  pDump_sanitize ($GLOBALS[$key]));
        else
          pDump_field ( $key, $GLOBALS[$key] ); 

    echo ( "\n" );

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
      elseif ( $key == 'pData' )
        $array [$key] = '*** pData ***';
      elseif ( $key == 'pCurrent' )
        $array [$key] = '*** pCurrent ***';
      elseif ( is_array ($val) )
        $array [$key] = pDump_sanitize ($val);
      elseif ( is_object($val) )
        $array [$key] = '***object***';
      elseif ( is_resource($val) )
        $array [$key] = '***resource***';
      elseif ( is_scalar($val) )
        $array [$key] = pDump_short ( $val );

    return $array;

  }


  function pDumpArray ( $txt, $arr) {

    if ( ! is_array($arr) ) {
      echo ( "\n  [$txt] => [todo, not array] \n");
      return;
    }

    if ( ! count ($arr )) {
      echo ( "\n  [$txt] => []");
      return;
    }

    $array = pDump_sanitize ($array);

    $p = htmlentities ( print_r ( $arr, TRUE ) ) ;

    $p = str_replace(" =&gt; Array\n" ,"\n", $p);
    $p = str_replace(")\n\n" ,")\n", $p);
    $p = preg_replace("/[\n]\s+\(/", "", $p);
    $p = preg_replace("/[\n]\s+\)/", "", $p);
    $p = str_replace("&lt;/address&gt;\n", "&lt;/address&gt;", $p);

    if ($x)
      echo ( "\n  [$txt]");
    else
      echo ( "\n<b>$txt</b>");

    if ($x)
      echo ( "\n" . substr($p, 8, strlen($p) - 11));
    else
      echo ( "\n" . substr($p, 8, strlen($p) - 10));

  }


  function pDump_object ( $txt, $arr) {

    $p = htmlentities ( print_r ( $arr, TRUE ) ) ;
    $p = preg_replace("/[\n]\(/", "", $p);
    $p = preg_replace("/[\n]\\)/", "", $p);
    $p = substr($p, 0,-1);

    echo ( "\n  [$txt] $p");

  }


?>