<?php

  function pad_trace_get_app_vars () { return pad_trace_get_xxx_vars ('app'); }
  function pad_trace_get_pad_vars () { return pad_trace_get_xxx_vars ('pad'); }
  function pad_trace_get_php_vars () { return pad_trace_get_xxx_vars ('php'); }

  function pad_trace_get_xxx_vars ($type) {

    $chk = ['_GET','_POST','_COOKIE','_FILES','_SESSION','_SERVER','_ENV','_REQUEST'];
    $not = ['app','page','PADSESSID','PADREFID','PADREQID'];

    $dump = [];

    foreach ($GLOBALS as $key => $value)
      if (    ( $type == 'app' and substr($key, 0, 3) <> 'pad' and ! in_array($key, $chk) and ! in_array($key, $not) ) 
           or ( $type == 'pad' and substr($key, 0, 3) == 'pad'                            )
           or ( $type == 'php' and in_array($key, $chk)                                   ) 
         )
        $dump [$key] = $value;

    return $dump;

  }


  function pad_trace_write_error ($error, $type, $count, $vars, $force=0 ) {

    if ( $GLOBALS['pad_error_dump'] and ! $GLOBALS['pad_trace_errors'] )
      return pad_trace_write_error_light ($error, $type, $count, $vars);

    if ( ! $force and ! $GLOBALS['pad_trace_errors'] )
      return;

    global $pad_occur_dir, $app, $page, $PADREQID;

    $pad_error_dir = "$pad_occur_dir/errors/$type/$count";

    $data = [];

    $data ['error']   = $error;
    $data ['number']  = $count;
    $data ['app']     = $app;
    $data ['page']    = $page;
    $data ['request'] = $PADREQID;
    $data ['dir']     = $pad_error_dir;

    foreach ($vars as $key => $val)
      $data [$key] = $val;

    pad_file_put_contents ( "errors/$PADREQID-$type-$count.json", $data ); 

    pad_file_put_contents ( "$pad_error_dir/error.json", $data ); 
    pad_file_put_contents ( "$pad_error_dir/pad.json",   pad_trace_get_pad_vars ()  );
    pad_file_put_contents ( "$pad_error_dir/app.json",   pad_trace_get_app_vars ()  );
    pad_file_put_contents ( "$pad_error_dir/php.json",   pad_trace_get_php_vars ()  );
    pad_file_put_contents ( "$pad_error_dir/dump.html",  pad_dump_get           ()  );

  }

  function pad_trace_write_error_light ($error, $type, $count, $vars) {

    global $app, $page, $PADREQID;

    pad_file_put_contents ( "errors/$PADREQID-$type-$count.html", pad_dump_get($error) ); 

  }

?>