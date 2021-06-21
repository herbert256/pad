<?php

  function pad_dump ($info='') {

    pad_empty_buffers ();

    gc_collect_cycles ();

    if ( ! headers_sent() )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    $GLOBALS['pad_sent'] = TRUE;

    pad_close_html();
    flush();

    pad_dump_vars ($info);

    pad_exit ();

  }  

  function pad_dump_vars ($info='') {

    $GLOBALS ['pad_dump_id'] = uniqid (TRUE);

    echo ("<div align=\"left\"><pre>");
    
    if ($info)
      echo ("<hr><b>$info</b><hr><br>");

    $pad_debug_backtrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);

    $not = ['pad_error','pad_dump','pad_dump_go','pad_error_go','pad_error_handler','pad_error_exception','pad_error_shutdown'];
    $not = [];

    echo ( "<b>Stack</b>\n");
    foreach ( $pad_debug_backtrace as $key => $trace ) {
      extract ( $trace );
       if ( isset($file) and isset($line) and isset($function) and ! in_array($function, $not) )
        echo ( "    $file:$line - $function\n");
    }

    pad_dump_array  ("Trace", $GLOBALS ['pad_trace_hist'] );

    if ( isset ( $GLOBALS ['pad_errors'] ) and is_array ( $GLOBALS ['pad_errors']) and count($GLOBALS ['pad_errors']) > 1 )
      pad_dump_array  ('Errors', $GLOBALS ['pad_errors'] );

    if ( isset($GLOBALS ['pad_lvl']) and $GLOBALS ['pad_lvl'] > 2 ) {
      echo ( "\n<b>Levels</b>\n");
      for ($i=$GLOBALS ['pad_lvl']; $i>1; $i--)
        echo ( "    $i - " . ($GLOBALS['pad_parameters'] [$i] ['name']??'???') . ' - ' . ($GLOBALS['pad_parameters'] [$i] ['parm']??'') . "\n");
    }

    if ( isset ( $GLOBALS ['pad_parameters'] ) )
      for ( $lvl=$GLOBALS ['pad_lvl'];  $lvl>1; $lvl-- ) 
        if ( isset($GLOBALS ['pad_parameters'] [$lvl] ) ) {
         $work = $GLOBALS ['pad_parameters'] [$lvl];
          foreach ($work as $key => $val)
            if ( is_scalar($val) )
              $work [$key] = substr(trim(preg_replace('/\s+/', ' ', $val) ), 0, 100);
          pad_dump_array  ('Level '.$lvl, $work );
          echo ("    [result] => " . htmlentities ( pad_dump_short ( $GLOBALS ['pad_result'] [$lvl] ) ) . "\n");
          echo ("    [base] => "   . htmlentities ( pad_dump_short ( $GLOBALS ['pad_base']   [$lvl] ) ) . "\n");
          echo ("    [html] => "   . htmlentities ( pad_dump_short ( $GLOBALS ['pad_html']   [$lvl] ) ) . "\n");
        }

    if ( isset ( $GLOBALS ['pad_data'] ) )
      for ( $lvl=$GLOBALS ['pad_lvl'];  $lvl>1; $lvl-- )
        if ( isset ($GLOBALS ['pad_data'][$lvl]) )
          pad_dump_array  ('Level '.$lvl, $GLOBALS ['pad_data'][$lvl] );

    if ( isset ( $_REQUEST ) and count ( $_REQUEST ) )
      pad_dump_array  ('Request variables', $_REQUEST);
      
    $app_chk = ['page','app','PADSESSID','PADREQID','PHPSESSID','PADREFID','GLOBALS','_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];
    $app_flds = FALSE;
    foreach ($GLOBALS as $key => $value)
      if ( substr($key, 0, 3) <> 'pad' and ! in_array($key, $app_chk ) )
        $app_flds = TRUE;
    if ( $app_flds ) {
      echo ( "<br><b>APP variables</b>");
      foreach ($GLOBALS as $key => $value)
        if ( substr($key, 0, 3) <> 'pad' and ! in_array($key, $app_chk ) )
          if (is_object($value))
            pad_dump_object ($key, $value);
          elseif (is_array ($value))
            pad_dump_array ($key, $value);
          else
            echo ( "\n  [$key] => " . htmlentities($value) );
      echo ( "\n ");
    }

    if ( isset ( $GLOBALS ['pad_sql_connect'     ] ) ) pad_dump_object ('MySQL-App', $GLOBALS ['pad_sql_connect']      );
    if ( isset ( $GLOBALS ['pad_pad_sql_connect' ] ) ) pad_dump_object ('MySQL-PAD', $GLOBALS ['pad_pad_sql_connect']  );

    $not = ['pad_lib_directory', 'pad_lib_iterator', 'pad_lib_one', 'pad_base','pad_sql_connect','pad_pad_sql_connect','pad_headers','pad_data','pad_parameters', 'pad_errors', 'pad_result', 'pad_html', 'pad_output', 'pad_output_gz', 'pad_current'];

    echo ( "\n<b>Pad variables</b>");

    $pad = [];
    foreach ( $GLOBALS as $key => $value )
      if ( substr($key, 0, 3)  == 'pad' and ! in_array ( $key, $not) )
        $pad [] = $key;
    sort($pad);
    
    foreach ($pad as $key)
      if (is_object($GLOBALS[$key]))
        pad_dump_object ($key, $GLOBALS[$key]);
      elseif (is_array ($GLOBALS[$key])) {
        $work =  pad_dump_sanitize ($GLOBALS[$key]);
        pad_dump_array ($key, $work, 1);
      }
      else
        echo ( "\n  [$key] => " . htmlentities(pad_dump_short($GLOBALS[$key])));

                                              pad_dump_array  ('Headers-in',  getallheaders());
    if ( isset ( $GLOBALS ['pad_headers'] ) ) pad_dump_array  ('Headers-out', $GLOBALS ['pad_headers'] );

    if ( isset ( $_GET )     )  pad_dump_array  ('GET',     $_GET);
    if ( isset ( $_POST )    )  pad_dump_array  ('POST',    $_POST);
    if ( isset ( $_COOKIE )  )  pad_dump_array  ('COOKIE',  $_COOKIE);
    if ( isset ( $_FILES )   )  pad_dump_array  ('FILES',   $_FILES);
    if ( isset ( $_SESSION ) )  pad_dump_array  ('SESSION', $_SESSION);  
    if ( isset ( $_SERVER )  )  pad_dump_array  ('SERVER',  $_SERVER);
    if ( isset ( $_ENV )     )  pad_dump_array  ('ENV',     $_ENV);

    echo ( "\n<b>GLOBALS</b>\n");
    echo ( htmlentities ( print_r ( $GLOBALS, TRUE ) ) );

    echo ( "\n</pre></div>" );

  }

  function pad_dump_short ($G) {
    if ( $G === NULL)
      $G = '';
    return substr ( preg_replace('/\s+/', ' ', $G ), 0, 100 );
  }

  function pad_dump_sanitize ($array) {

    foreach ($array as $key => $val)
      if ( $key == 'GLOBALS' )
        $array [$key] = '*** GLOBALS ***';
      elseif ( $key == 'pad_data' )
        $array [$key] = '*** pad_data ***';
      elseif ( $key == 'pad_current' )
        $array [$key] = '*** pad_current ***';
      elseif ( is_array ($val) )
        $array [$key] = pad_dump_sanitize ($val);
      elseif ( is_object($val) )
        $array [$key] = '***object***';
      elseif ( is_resource($val) )
        $array [$key] = '***resource***';
      elseif ( is_scalar($val) )
        $array [$key] = pad_dump_short ( $val );

    return $array;

  }

  function pad_dump_array ( $txt, $arr, $x=0) {

    if ( ! is_array($arr) ) {
      echo ( "\n  [$txt] => [todo, not array] \n");
      return;
    }

    if ( $x and ! count ($arr )) {
      echo ( "\n  [$txt] => [empty array]");
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

  function pad_dump_object ( $txt, $arr) {

    $p = htmlentities ( print_r ( $arr, TRUE ) ) ;

    echo ( "\n<b>$txt:</b>");
    echo ( "\n" . $p);

  }

?>