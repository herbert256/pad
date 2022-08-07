<?php


  function pDump ($info='') {

    if ( ! pLocal () )
      return;

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

    $pDebug_backtrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);

    echo ( "<pre><b>$info</b>\n");
    foreach ( $pDebug_backtrace as $key => $trace ) {
      extract ( $trace );
      echo ( "    $file:$line - $function\n");
    }

    pDump_array  ( 'go', $GLOBALS ,     1 );

    exit;



    $app_chk = ['page','app','PADSESSID','PADREQID','PHPSESSID','PADREFID','GLOBALS','_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];

    $not = ['pBase','pSql_connect','pPad_sql_connect','pHeaders','pData','pParms', 'pResult', 'pHtml', 'pOutput', 'pOutput_gz', 'pCurrent', 'pLib_directory', 'pLib_iterator', 'pLib_one'];

    $ids = [ 'session' => $GLOBALS['PADSESSID']??'', 'request' => $GLOBALS['PADREQID']??'', 'reference' => $GLOBALS['PADREFID']??'' ];

    $settings = '';
    if ( file_exists (PAD . 'config/config.php') )
      $settings .= pFile_get_contents(PAD . 'config/config.php');

    if ( defined('APP'))
       if ( file_exists (APP . 'config/config.php') )
          $settings .= pFile_get_contents(APP . 'config/config.php');

    $p = $config = $app_flds = [];

    foreach ($GLOBALS as $key => $value) {

      if ( substr($key, 0, 3) <> 'p' and ! in_array($key, $app_chk ) )
        $app_flds [] = $key;
 
      if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t"))
        $config [] = $key;

      if ( substr($key, 0, 1)  == 'p' and ! in_array ($key, $not)  and ! in_array ($key, $config) )
        $p [] = $key;

    }

    sort($app_flds);
    sort($config);
    sort($pad);

    $pDebug_backtrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);

    echo ("<div align=\"left\"><pre>");

    if ($info)
      echo ("<hr><b>$info</b><hr><br>");

    if ( isset($GLOBALS ['pIn_sequence'] ) and $GLOBALS ['pIn_sequence'] === TRUE ) {

      $seq = [];
      
      foreach ( $pad as $key )
        if ( substr($key, 0, 7) == 'pSeq') {
          unset ($pad[$key]);
          $seq [] = $key;
        }   

      pDump_fields ($seq, "Sequence variables");

      echo ( "\n\n");

    }

    if ( isset($GLOBALS ['pTrace_stage'] ) and $GLOBALS ['pTrace_stage'] <> 'end' ) {

      echo ( "<b>Eval details</b>\n");

      pDump_field  ( 'eval',   $GLOBALS ['pTrace_eval']      );
      pDump_field  ( 'myself', $GLOBALS ['pTrace_myself']    );

      if ( count ( $GLOBALS ['pTrace_now'] ) )
        pDump_array  ( 'now', $GLOBALS ['pTrace_now'], 1 );

      pDump_array  ( 'parsed', $GLOBALS ['pTrace_parsed'], 1 );
      pDump_array  ( 'after',  $GLOBALS ['pTrace_after'],  1 );
      pDump_array  ( 'go',     $GLOBALS ['pTrace_go'],     1 );
      pDump_array  ( 'result', $GLOBALS ['pEval_result'],       1 );

      echo ( "\n\n");

    }

    echo ( "<b>Stack</b>\n");
    foreach ( $pDebug_backtrace as $key => $trace ) {
      extract ( $trace );
      echo ( "    $file:$line - $function\n");
    }

    if ( isset ( $GLOBALS ['pParms'] ) )
      for ( $lvl=$GLOBALS ['pad'];  $lvl>0; $lvl-- ) 
        if ( isset($GLOBALS ['pParms'] [$lvl] ) ) {

          $work = $GLOBALS ['pParms'] [$lvl];
          foreach ($work as $key => $val)
            if ( is_scalar($val) )
              $work [$key] = substr(trim(preg_replace('/\s+/', ' ', $val) ), 0, 100);

          pDump_array  ('Level '.$lvl, $work );
  
          if ( isset($GLOBALS ['pBase'] [$lvl]) and $GLOBALS ['pBase'] [$lvl] )
            echo ("    [base] => "   . htmlentities ( pDump_short ( $GLOBALS ['pBase'] [$lvl] ) ) . "\n");

          if ( isset($GLOBALS ['pResult'] [$lvl]) and $GLOBALS ['pResult'] [$lvl] and $GLOBALS ['pResult'] <> $GLOBALS ['pBase'] )
            echo ("    [result] => " . htmlentities ( pDump_short ( $GLOBALS ['pResult'] [$lvl] ) ) . "\n");
  
          if ( isset($GLOBALS ['pHtml'] [$lvl]) and $GLOBALS ['pHtml'] [$lvl] and $GLOBALS ['pHtml'] <> $GLOBALS ['pBase']and $GLOBALS ['pHtml'] <>  $GLOBALS ['pResult'])
            echo ("    [html] => "   . htmlentities ( pDump_short ( $GLOBALS ['pHtml']    [$lvl] ) ) . "\n");

        }

    if ( isset ( $GLOBALS ['pData'] ) and is_array ( $GLOBALS ['pData'] ) )
      for ( $lvl=$GLOBALS ['pad'];  $lvl>1; $lvl-- )
        if ( isset ($GLOBALS ['pData'][$lvl]) )
          pDump_array  ('Level '.$lvl, $GLOBALS ['pData'][$lvl] );

    if ( isset ( $_REQUEST ) and count ( $_REQUEST ) )
      pDump_array  ('Request variables', $_REQUEST);
 
    pDump_array  ("ID's", $ids);
 
    pDump_fields ($app_flds, "APP variables");

    if ( isset ( $GLOBALS ['pSql_connect'     ] ) ) pDump_object ('MySQL-App', $GLOBALS ['pSql_connect']      );
    if ( isset ( $GLOBALS ['pPad_sql_connect' ] ) ) pDump_object ('MySQL-PAD', $GLOBALS ['pPad_sql_connect']  );

    pDump_fields ($p,    "Pad variables");
    pDump_fields ($config, "Settings");
                                              pDump_array  ('Headers-in',  getallheaders());
    if ( isset ( $GLOBALS ['pHeaders'] ) ) pDump_array  ('Headers-out', $GLOBALS ['pHeaders'] );

    if ( isset ( $_GET )     )  pDump_array  ('GET',     $_GET);
    if ( isset ( $_POST )    )  pDump_array  ('POST',    $_POST);
    if ( isset ( $_COOKIE )  )  pDump_array  ('COOKIE',  $_COOKIE);
    if ( isset ( $_FILES )   )  pDump_array  ('FILES',   $_FILES);
    if ( isset ( $_SESSION ) )  pDump_array  ('SESSION', $_SESSION);  
    if ( isset ( $_SERVER )  )  pDump_array  ('SERVER',  $_SERVER);
    if ( isset ( $_ENV )     )  pDump_array  ('ENV',     $_ENV);

    pDump_fields ($not, "Pad variables - part 2 ");

    echo ( "\n</pre></div>" );

  }


  function pDump_fields ($fields, $text) {

    echo ( "\n<b>$text</b>");

    foreach ($fields as $key)
      if ( isset($GLOBALS[$key]) )
        if (is_object($GLOBALS[$key]))
          pDump_object ($key, $GLOBALS[$key]);
        elseif (is_array ($GLOBALS[$key]))
          pDump_array ($key,  pDump_sanitize ($GLOBALS[$key]), 1);
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

  function pDump_array ( $txt, $arr, $x=0) {

    if ( ! is_array($arr) ) {
      echo ( "\n  [$txt] => [todo, not array] \n");
      return;
    }

    if ( $x and ! count ($arr )) {
      echo ( "\n  [$txt] => []");
      return;
    }

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