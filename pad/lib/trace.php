<?php

  function pTraceGetLevel ($p)  {

    return [
      'tag' => $GLOBALS['pTag'][$p]??'',
      't-type' => $GLOBALS['pType'][$p]??'',
      'pair' => $GLOBALS['pPair'][$p]??'',
      'p-type' => $GLOBALS['pPrmsType'][$p]??'',
      'prm' => $GLOBALS['pPrm'][$p]??'',
      'prms' => $GLOBALS['pPrms'][$p]??'',
      'flags' => $GLOBALS['pPrmsTag'][$p]??'',
      'values' => $GLOBALS['pPrmsVal'][$p]??'',
      'true' => pDump_short ($GLOBALS['pTrue'][$p]??''),
      'false' => pDump_short ($GLOBALS['pFalse'][$p]??''),
      'base' => pDump_short ($GLOBALS['pBase'][$p]??''),
      'html' => pDump_short ($GLOBALS['pHtml'][$p]??''),
      'result' => pDump_short ($GLOBALS['pResult'][$p]??''),
      'name' => $GLOBALS['pName'][$p]??'',
      'default' => $GLOBALS['pDefault'][$p]??'',
      'walk' => $GLOBALS['pWalk'][$p]??'',
      'hit' => $GLOBALS['pHit'][$p]??'',
      'null' => $GLOBALS['pNull'][$p]??'',
      'else' => $GLOBALS['pElse'][$p]??'',
      'array' => $GLOBALS['pArray'][$p]??'',
      'text' => $GLOBALS['pText'][$p]??''
    ];

  } 


  function pTrace_write_error ($error, $type, $count, $vars, $force=0 ) {

#    if ( $GLOBALS['pError_dump'] and ! $GLOBALS['pTrace'] )
      return pTrace_write_error_light ($error, $type, $count, $vars);

    if ( ! $force and ! $GLOBALS['pTrace'] )
      return;

    global $p, $pOccurDir, $app, $page, $PADREQID;

    $pError_dir = $pOccurDir [$p] . "/error-$count-$type";

    $data = [];

    $data ['error']   = $error;
    $data ['number']  = $count;
    $data ['dir']     = DATA . $pError_dir;

    foreach ($vars as $key => $val)
      $data [$key] = $val;

    pFile_put_contents ( "errors/$PADREQID-$count-$type.json", $data ); 
    pFile_put_contents ( "$pError_dir/error.json",             $data ); 
    
    pTraceAll ( $pError_dir );

  }


  function pTrace_write_error_light ($error, $type, $count, $vars) {

    global $PADREQID;

    pFile_put_contents ( "errors/$PADREQID-$type-$count.html", pDump_get($error) ); 

  }


  function pTraceAll ( $dir ) {

    pFields ( $pFphp, $pFlvl, $pFapp, $pFcfg, $pFpad, $pFids );

    pFile_put_contents ( "$dir/pad.json",   $pFpad  );
    pFile_put_contents ( "$dir/app.json",   $pFapp  );
    pFile_put_contents ( "$dir/php.json",   $pFphp  );
    pFile_put_contents ( "$dir/levels.json",$pFlvl  );
    pFile_put_contents ( "$dir/ids.json",   $pFids  );
    pFile_put_contents ( "$dir/config.json",$pFcfg  );

  }

?>