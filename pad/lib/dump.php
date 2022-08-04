<?php


  function pad_dump ($info='') {

    if ( ! pad_local () )
      return;

    pad_empty_buffers ();
    gc_collect_cycles ();
    pad_close_html    ();
    pad_dump_vars     ($info);

    $GLOBALS ['pad_sent'] = TRUE;

    pad_stop (500);

  }   


  function pad_dump_to_file ($file, $info='') {

    pad_file_put_contents ( $file, pad_dump_get ($info) );
        
  }


  function pad_dump_get ($info='') {

    ob_start();

    pad_dump_vars ($info);

    return ob_get_clean();
        
  }


  function pad_dump_vars ($info='') {

    $app_chk = ['page','app','PADSESSID','PADREQID','PHPSESSID','PADREFID','GLOBALS','_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];

    $not = ['pad_base','pad_sql_connect','pad_pad_sql_connect','pad_headers','pad_data','pad_parms', 'pad_result', 'pad_html', 'pad_output', 'pad_output_gz', 'pad_current', 'pad_lib_directory', 'pad_lib_iterator', 'pad_lib_one'];

    $ids = [ 'session' => $GLOBALS['PADSESSID']??'', 'request' => $GLOBALS['PADREQID']??'', 'reference' => $GLOBALS['PADREFID']??'' ];

    $settings = '';
    if ( file_exists (PAD . 'config/config.php') )
      $settings .= pad_file_get_contents(PAD . 'config/config.php');

    if ( defined('APP'))
       if ( file_exists (APP . 'config/config.php') )
          $settings .= pad_file_get_contents(APP . 'config/config.php');

    $pad = $config = $app_flds = [];

    foreach ($GLOBALS as $key => $value) {

      if ( substr($key, 0, 3) <> 'pad' and ! in_array($key, $app_chk ) )
        $app_flds [] = $key;
 
      if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t"))
        $config [] = $key;

      if ( substr($key, 0, 3)  == 'pad' and ! in_array ($key, $not)  and ! in_array ($key, $config) )
        $pad [] = $key;

    }

    sort($app_flds);
    sort($config);
    sort($pad);

    $pad_debug_backtrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);

    echo ("<div align=\"left\"><pre>");

    if ($info)
      echo ("<hr><b>$info</b><hr><br>");

    if ( isset($GLOBALS ['pad_in_sequence'] ) and $GLOBALS ['pad_in_sequence'] === TRUE ) {

      $seq = [];
      
      foreach ( $pad as $key )
        if ( substr($key, 0, 7) == 'pad_seq') {
          unset ($pad[$key]);
          $seq [] = $key;
        }   

      pad_dump_fields ($seq, "Sequence variables");

      echo ( "\n\n");

    }

    if ( isset($GLOBALS ['pad_trace_eval_stage'] ) and $GLOBALS ['pad_trace_eval_stage'] <> 'end' ) {

      echo ( "<b>Eval details</b>\n");

      pad_dump_field  ( 'eval',   $GLOBALS ['pad_trace_eval_eval']      );
      pad_dump_field  ( 'myself', $GLOBALS ['pad_trace_eval_myself']    );

      if ( count ( $GLOBALS ['pad_trace_eval_now'] ) )
        pad_dump_array  ( 'now', $GLOBALS ['pad_trace_eval_now'], 1 );

      pad_dump_array  ( 'parsed', $GLOBALS ['pad_trace_eval_parsed'], 1 );
      pad_dump_array  ( 'after',  $GLOBALS ['pad_trace_eval_after'],  1 );
      pad_dump_array  ( 'go',     $GLOBALS ['pad_trace_eval_go'],     1 );
      pad_dump_array  ( 'result', $GLOBALS ['pad_eval_result'],       1 );

      echo ( "\n\n");

    }

    echo ( "<b>Stack</b>\n");
    foreach ( $pad_debug_backtrace as $key => $trace ) {
      extract ( $trace );
      echo ( "    $file:$line - $function\n");
    }

    if ( isset ( $GLOBALS ['pad_parms'] ) )
      for ( $lvl=$GLOBALS ['pad'];  $lvl>0; $lvl-- ) 
        if ( isset($GLOBALS ['pad_parms'] [$lvl] ) ) {

          $work = $GLOBALS ['pad_parms'] [$lvl];
          foreach ($work as $key => $val)
            if ( is_scalar($val) )
              $work [$key] = substr(trim(preg_replace('/\s+/', ' ', $val) ), 0, 100);

          pad_dump_array  ('Level '.$lvl, $work );
  
          if ( isset($GLOBALS ['pad_base'] [$lvl]) and $GLOBALS ['pad_base'] [$lvl] )
            echo ("    [base] => "   . htmlentities ( pad_dump_short ( $GLOBALS ['pad_base'] [$lvl] ) ) . "\n");

          if ( isset($GLOBALS ['pad_result'] [$lvl]) and $GLOBALS ['pad_result'] [$lvl] and $GLOBALS ['pad_result'] <> $GLOBALS ['pad_base'] )
            echo ("    [result] => " . htmlentities ( pad_dump_short ( $GLOBALS ['pad_result'] [$lvl] ) ) . "\n");
  
          if ( isset($GLOBALS ['pad_html'] [$lvl]) and $GLOBALS ['pad_html'] [$lvl] and $GLOBALS ['pad_html'] <> $GLOBALS ['pad_base']and $GLOBALS ['pad_html'] <>  $GLOBALS ['pad_result'])
            echo ("    [html] => "   . htmlentities ( pad_dump_short ( $GLOBALS ['pad_html']    [$lvl] ) ) . "\n");

        }

    if ( isset ( $GLOBALS ['pad_data'] ) and is_array ( $GLOBALS ['pad_data'] ) )
      for ( $lvl=$GLOBALS ['pad'];  $lvl>1; $lvl-- )
        if ( isset ($GLOBALS ['pad_data'][$lvl]) )
          pad_dump_array  ('Level '.$lvl, $GLOBALS ['pad_data'][$lvl] );

    if ( isset ( $_REQUEST ) and count ( $_REQUEST ) )
      pad_dump_array  ('Request variables', $_REQUEST);
 
    pad_dump_array  ("ID's", $ids);
 
    pad_dump_fields ($app_flds, "APP variables");

    if ( isset ( $GLOBALS ['pad_sql_connect'     ] ) ) pad_dump_object ('MySQL-App', $GLOBALS ['pad_sql_connect']      );
    if ( isset ( $GLOBALS ['pad_pad_sql_connect' ] ) ) pad_dump_object ('MySQL-PAD', $GLOBALS ['pad_pad_sql_connect']  );

    pad_dump_fields ($pad,    "Pad variables");
    pad_dump_fields ($config, "Settings");
                                              pad_dump_array  ('Headers-in',  getallheaders());
    if ( isset ( $GLOBALS ['pad_headers'] ) ) pad_dump_array  ('Headers-out', $GLOBALS ['pad_headers'] );

    if ( isset ( $_GET )     )  pad_dump_array  ('GET',     $_GET);
    if ( isset ( $_POST )    )  pad_dump_array  ('POST',    $_POST);
    if ( isset ( $_COOKIE )  )  pad_dump_array  ('COOKIE',  $_COOKIE);
    if ( isset ( $_FILES )   )  pad_dump_array  ('FILES',   $_FILES);
    if ( isset ( $_SESSION ) )  pad_dump_array  ('SESSION', $_SESSION);  
    if ( isset ( $_SERVER )  )  pad_dump_array  ('SERVER',  $_SERVER);
    if ( isset ( $_ENV )     )  pad_dump_array  ('ENV',     $_ENV);

    pad_dump_fields ($not, "Pad variables - part 2 ");

    echo ( "\n</pre></div>" );

  }


  function pad_dump_fields ($fields, $text) {

    echo ( "\n<b>$text</b>");

    foreach ($fields as $key)
      if ( isset($GLOBALS[$key]) )
        if (is_object($GLOBALS[$key]))
          pad_dump_object ($key, $GLOBALS[$key]);
        elseif (is_array ($GLOBALS[$key]))
          pad_dump_array ($key,  pad_dump_sanitize ($GLOBALS[$key]), 1);
        else
          pad_dump_field ( $key, $GLOBALS[$key] ); 

    echo ( "\n" );

  }

  function pad_dump_field ($field, $value) {
    echo ( "\n  [$field] => " . htmlentities(pad_dump_short($value??''))); 
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

  function pad_dump_object ( $txt, $arr) {

    $p = htmlentities ( print_r ( $arr, TRUE ) ) ;
    $p = preg_replace("/[\n]\(/", "", $p);
    $p = preg_replace("/[\n]\\)/", "", $p);
    $p = substr($p, 0,-1);

    echo ( "\n  [$txt] $p");

  }


?>