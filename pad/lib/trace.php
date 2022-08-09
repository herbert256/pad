<?php

  function pTraceGetVars ()  { 

    $dump = [];

    if ( isset ( $GLOBALS ['p'] ) and $GLOBALS ['p'] > 0 ) {
      for ( $p=$GLOBALS ['p'];  $p>0; $p-- ) {
        $dump [$p] = [
          'tag' => $GLOBALS['pTag'][$p],
          'prms' => $GLOBALS['pPrms'][$p],
          't-type' => $GLOBALS['pType'][$p],
          'prm' => $GLOBALS['pPrm'][$p],
          'pair' => $GLOBALS['pPair'][$p],
          'true' => pDump_short ($GLOBALS['pTrue'][$p]),
          'false' => pDump_short ($GLOBALS['pFalse'][$p]),
          'base' => pDump_short ($GLOBALS['pBase'][$p]),
          'html' => pDump_short ($GLOBALS['pHtml'][$p]),
          'result' => pDump_short ($GLOBALS['pResult'][$p]),
          'p-type' => $GLOBALS['pPrmsType'][$p],
          'flags' => $GLOBALS['pPrmsTag'][$p],
          'values' => $GLOBALS['pPrmsVal'][$p],
          'name' => $GLOBALS['pName'][$p],
          'default' => $GLOBALS['pDefault'][$p],
          'walk' => $GLOBALS['pWalk'][$p],
          't-result' => $GLOBALS['pTagResult'][$p],
          'hit' => $GLOBALS['pHit'][$p],
          'null' => $GLOBALS['pNull'][$p],
          'else' => $GLOBALS['pElse'][$p],
          'array' => $GLOBALS['pArray'][$p],
          'text' => $GLOBALS['pText'][$p]
        ];
      } 
    }

    if ( isset ( $GLOBALS ['p'] ) and $GLOBALS ['p'] > 0 ) {
      for ( $p=$GLOBALS ['p'];  $p>0; $p-- ) {
        $dump ['data'] [$p] = $GLOBALS ['pData'] [$p];
      } 
    }
    
    $dump ['GLOBALS'] = $GLOBALS;

    return $dump;

  }


  function pTrace_get_app_vars () { return pTrace_get_xxx_vars ('app'); }
  function pTrace_get_php_vars () { return pTrace_get_xxx_vars ('php'); }

  function pTrace_get_xxx_vars ($type) {

    $chk = ['_GET','_POST','_COOKIE','_FILES','_SESSION','_SERVER','_ENV','_REQUEST'];
    $not = ['app','page','PADSESSID','PADREFID','PADREQID'];

    $dump = [];

    foreach ($GLOBALS as $key => $value)
      if (    ( $type == 'app' and substr($key, 0, 1) <> 'p' and ! in_array($key, $chk) and ! in_array($key, $not) ) 
           or ( $type == 'pad' and substr($key, 0, 1) == 'p'                            )
           or ( $type == 'php' and in_array($key, $chk)                                   ) 
         )
        $dump [$key] = $value;

    ksort ($dump);

    return $dump;

  }


  function pTrace_write_error ($error, $type, $count, $vars, $force=0 ) {

    if ( $GLOBALS['pError_dump'] and ! $GLOBALS['pTrace'] )
      return pTrace_write_error_light ($error, $type, $count, $vars);

    if ( ! $force and ! $GLOBALS['pTrace'] )
      return;

    global $p, $pOccurDir, $app, $page, $PADREQID;

    $pError_dir = $pOccurDir [$p] . "/error-$count-$type";

    $data = [];

    $data ['error']   = $error;
    $data ['number']  = $count;
    $data ['app']     = $app;
    $data ['page']    = $page;
    $data ['request'] = $PADREQID;
    $data ['dir']     = DATA . $pError_dir;

    foreach ($vars as $key => $val)
      $data [$key] = $val;

    pFile_put_contents ( "errors/$PADREQID-$count-$type.json", $data ); 

    pFile_put_contents ( "$pError_dir/error.json", $data ); 
    pFile_put_contents ( "$pError_dir/pad.json",   pTraceGetVars ()  );
    pFile_put_contents ( "$pError_dir/app.json",   pTrace_get_app_vars ()  );
    pFile_put_contents ( "$pError_dir/php.json",   pTrace_get_php_vars ()  );
    pFile_put_contents ( "$pError_dir/dump.html",  pDump_get           ()  );

  }

  function pTrace_write_error_light ($error, $type, $count, $vars) {

    global $app, $page, $PADREQID;

    pFile_put_contents ( "errors/$PADREQID-$type-$count.html", pDump_get($error) ); 

  }

?>