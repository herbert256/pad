<?php

  function pad_dump ($info='') {

    gc_collect_cycles();
    
    if ( isset($GLOBALS['pad_sent']) and !isset($GLOBALS['pad_track_vars_file']) )
      pad_close_html();

    pad_dump_item ("<div align=\"left\"><pre>");

    if ($info)
      pad_dump_item ("<hr><b>$info</b><hr><br>");

    $pad_debug_backtrace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);

    pad_dump_item ( "<b>Stack</b>\n");
    foreach ( $pad_debug_backtrace as $key => $trace ) {
      extract ( $trace );
      if ( isset($file) and isset($line) and isset($function) )
        pad_dump_item ( "    $file:$line - $function\n");
    }

  pad_dump_item ( "\n<b>Current:</b> {" . ($GLOBALS['pad_between'] ?? '???') . "}\n");

  if ( isset($GLOBALS ['pad_lvl']) and $GLOBALS ['pad_lvl'] > 2 ) {
    pad_dump_item ( "\n<b>Levels 1</b>\n");
    for ($i=$GLOBALS ['pad_lvl']; $i; $i--) { 
      pad_dump_item ( "    $i - " . ($GLOBALS['pad_parameters'] [$i] ['name']??'???') . ' - ' . ($GLOBALS['pad_parameters'] [$i] ['parm']??'') . "\n");
    }

  }

   if ( isset ( $GLOBALS ['pad_parameters'] ) ) 
    if ( isset ( $GLOBALS ['pad_lvl'] ) and $GLOBALS ['pad_lvl'] > 1 ) {
      if ( count ($GLOBALS ['pad_parameters'] [$GLOBALS ['pad_lvl']] ) )
        $lvl = $GLOBALS ['pad_lvl'];
      else
        $lvl = $GLOBALS ['pad_lvl'] - 1;
      $work = $GLOBALS ['pad_parameters'] [$lvl];
      foreach ($work as $key => $val)
        if ( is_scalar($val) )
          $work [$key] = substr(trim(preg_replace('/\s+/', ' ', $val) ), 0, 100);
      pad_dump_array  ("Level: $lvl", $work );
    }

    if ( isset ( $GLOBALS ['pad_trace_log'] ) )
      pad_dump_array  ("Trace", $GLOBALS ['pad_trace_log'] );

    if ( isset ( $GLOBALS ['pad_errors'] ) and is_array ( $GLOBALS ['pad_errors']) and count($GLOBALS ['pad_errors']) > 1 )
      pad_dump_array  ('Errors', $GLOBALS ['pad_errors'] );

    pad_dump_item ( "<br><b>APP variables</b>");
    foreach ($GLOBALS as $key => $value)
      if ( substr($key, 0, 3) <> 'pad' and ! in_array($key, ['page','app','PADREQID','PHPSESSID','PADSESSID','GLOBALS','_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'] ) ) {
        if (is_object($value))
          pad_dump_object ($key, $value);
        elseif (is_array ($value))
          pad_dump_array ($key, $value);
        else
          pad_dump_item ( "\n  [$key] => " . htmlentities($value) );
      }

    pad_dump_item ( "\n ");

    if ( isset ( $GLOBALS ['pad_parameters'] ) )
      foreach ($GLOBALS ['pad_parameters'] as $key => $val)
        pad_dump_array ('$pad_parameters['.$key.']', $GLOBALS ['pad_parameters'][$key]  );

    if ( isset ( $GLOBALS ['pad_lvl'] ) )
      pad_dump_item ( "\n<b>Level: ". $GLOBALS['pad_lvl'] . "</b>\n");

   if ( isset ( $GLOBALS ['pad_key'] ) )
          pad_dump_array ('$pad_key', $GLOBALS ['pad_key']  );

    if ( isset ( $GLOBALS ['pad_data'] ) )
      foreach ($GLOBALS ['pad_data'] as $key => $val)
        if ($key > 1)
          pad_dump_array ('$pad_data['.$key.']', $GLOBALS ['pad_data'][$key]  );
      
    if ( isset ( $GLOBALS ['pad_headers'] ) )
      pad_dump_array  ('Headers-out',  $GLOBALS ['pad_headers'] );

   if ( isset ( $GLOBALS ['pad_db'] ) )
      pad_dump_array  ('db', $GLOBALS ['pad_db'] );

    if ( isset ( $GLOBALS ['pad_db_lvl'] ) )
      pad_dump_array  ('db_lvl', $GLOBALS ['pad_db_lvl'] );

    if ( isset ( $_REQUEST ) )
      pad_dump_array  ('Request variables', $_REQUEST);
      
    if ( isset ( $GLOBALS ['pad_sql_connect'     ] ) ) pad_dump_object ('MySQL-App', $GLOBALS ['pad_sql_connect']      );
    if ( isset ( $GLOBALS ['pad_pad_sql_connect' ] ) ) pad_dump_object ('MySQL-PAD', $GLOBALS ['pad_pad_sql_connect']  );

    $pad = [];
    $not = ['pad_base','pad_sql_connect','pad_pad_sql_connect','pad_headers','pad_data','pad_parameters', 'pad_errors', 'pad_result', 'pad_html', 'pad_output', 'pad_output_gz', 'pad_current'];

    pad_dump_item ( "\n<b>Internal Pad variables</b>");

    foreach ( $GLOBALS as $key => $value )
      if ( substr($key, 0, 3)  == 'pad' and ! in_array ( $key, $not) )
        $pad [] = $key;
    sort($pad);
    
    foreach ($pad as $key)
      if ( ! in_array($key, ['pad_data']))
        if (is_object($GLOBALS[$key]))
          pad_dump_object ($key, $GLOBALS[$key]);
        elseif (is_array ($GLOBALS[$key]))
          pad_dump_array ($key, $GLOBALS[$key], 1);
        else
          pad_dump_item ( "\n  [$key] => " . htmlentities($GLOBALS[$key]));

    if ( isset ( $_GET )     )  pad_dump_array  ('GET',     $_GET);
    if ( isset ( $_POST )    )  pad_dump_array  ('POST',    $_POST);
    if ( isset ( $_COOKIE )  )  pad_dump_array  ('COOKIE',  $_COOKIE);
    if ( isset ( $_FILES )   )  pad_dump_array  ('FILES',   $_FILES);
    if ( isset ( $_SESSION ) )  pad_dump_array  ('SESSION', $_SESSION);
                                pad_dump_array  ('HEADERS', getallheaders());
    if ( isset ( $_SERVER )  )  pad_dump_array  ('SERVER',  $_SERVER);
    if ( isset ( $_ENV )     )  pad_dump_array  ('ENV',     $_ENV);

    pad_dump_array ( 'Loaded files', get_included_files() );

    $pad = $app = [];
    $user = get_defined_functions () ['user'];

    foreach ($user as $fun)
      if ( substr($fun, 0, 4) == 'pad_' )
        $pad [] = $fun;
      else
        $app [] = $fun;

    pad_dump_array ( 'APP functions', $app );

    $pad = [];
    foreach ( $GLOBALS as $key => $value )
      if ( substr($key, 0, 3)  == 'pad' and in_array ( $key, $not) )
        $pad [] = $key;
    sort($pad);
    
    foreach ($pad as $key)
      if ( ! in_array($key, ['pad_data']))
        if (is_object($GLOBALS[$key]))
          pad_dump_object ($key, $GLOBALS[$key]);
        elseif (is_array ($GLOBALS[$key]))
          pad_dump_array ($key, $GLOBALS[$key], 1);
        else
          pad_dump_item ( "\n  [$key] => " . htmlentities($GLOBALS[$key]));

    if ( isset ( $GLOBALS ['pad_data'][1] ) )
     pad_dump_array ('$pad_data[1]', $GLOBALS ['pad_data'][1]  );

    pad_dump_item ( "</pre></div>");

  }


  function pad_php_internals () {
    
    pad_dump_item ( "<br><br><hr><b>PHP Internals</b><hr>") ;

    pad_dump_array ( 'get_declared_interfaces', get_declared_interfaces () );
    pad_dump_array ( 'get_declared_traits',     get_declared_traits () );
    pad_dump_array ( 'get_declared_classes',    get_declared_classes () );

    $modules = get_loaded_extensions ();
    pad_dump_array ('get_loaded_extensions', $modules);
    foreach ($modules as $module)
      pad_dump_array ($module, get_extension_funcs($module));

    pad_dump_array ( 'get_defined_constants', get_defined_constants () );
    
  }
  
  function pad_dump_array ( $txt, $arr, $x=0) {

    if ( $x and ! count ($arr )) {
      pad_dump_item ( "\n  [$txt] => [empty array]");
      return;
    }

    if ( is_array($arr) ) {
 
      $p = htmlentities ( print_r ( $arr, TRUE ) ) ;

      $p = str_replace(" =&gt; Array\n" ,"\n", $p);
      $p = str_replace(")\n\n" ,")\n", $p);
      $p = preg_replace("/[\n]\s+\(/", "", $p);
      $p = preg_replace("/[\n]\s+\)/", "", $p);
      $p = str_replace("&lt;/address&gt;\n", "&lt;/address&gt;", $p);

      if ($x)
        pad_dump_item ( "\n  [$txt]");
      else
        pad_dump_item ( "\n<b>$txt</b>");

      if ($x)
        pad_dump_item ( "\n" . substr($p, 8, strlen($p) - 11));
      else
        pad_dump_item ( "\n" . substr($p, 8, strlen($p) - 10));

    }

  }

  function pad_dump_object ( $txt, $arr) {

    $p = htmlentities ( print_r ( $arr, TRUE ) ) ;

    pad_dump_item ( "\n<b>$txt:</b>");
    pad_dump_item ( "\n" . $p);

  }

  function pad_dump_item ($item, $echo=1) {
    
    if ( isset($GLOBALS['pad_track_vars_file']) )

        pad_file_put_contents ($GLOBALS['pad_track_vars_file'], $item, 1);
    
    else {
      
      if ( pad_local() and $echo) {
        echo $item;
        $GLOBALS['pad_sent'] = TRUE;
      }

    }
    
  }

?>