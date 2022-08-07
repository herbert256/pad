<?php

  function pTrace_get_app_vars () { return pTrace_get_xxx_vars ('app'); }
  function pTrace_get_pVars () { return pTrace_get_xxx_vars ('pad'); }
  function pTrace_get_php_vars () { return pTrace_get_xxx_vars ('php'); }

  function pTrace_get_xxx_vars ($type) {

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


  function pTrace_write_error ($error, $type, $count, $vars, $force=0 ) {

    if ( $GLOBALS['pError_dump'] and ! $GLOBALS['pTrace_errors'] )
      return pTrace_write_error_light ($error, $type, $count, $vars);

    if ( ! $force and ! $GLOBALS['pTrace_errors'] )
      return;

    global $pOccurDir, $app, $page, $PADREQID;

    $pError_dir = "$pOccurDir/errors/$type/$count";

    $data = [];

    $data ['error']   = $error;
    $data ['number']  = $count;
    $data ['app']     = $app;
    $data ['page']    = $page;
    $data ['request'] = $PADREQID;
    $data ['dir']     = $pError_dir;

    foreach ($vars as $key => $val)
      $data [$key] = $val;

    pFile_put_contents ( "errors/$PADREQID-$type-$count.json", $data ); 

    pFile_put_contents ( "$pError_dir/error.json", $data ); 
    pFile_put_contents ( "$pError_dir/pad.json",   pTrace_get_pVars ()  );
    pFile_put_contents ( "$pError_dir/app.json",   pTrace_get_app_vars ()  );
    pFile_put_contents ( "$pError_dir/php.json",   pTrace_get_php_vars ()  );
    pFile_put_contents ( "$pError_dir/dump.html",  pDump_get           ()  );

  }

  function pTrace_write_error_light ($error, $type, $count, $vars) {

    global $app, $page, $PADREQID;

    pFile_put_contents ( "errors/$PADREQID-$type-$count.html", pDump_get($error) ); 

  }

?>