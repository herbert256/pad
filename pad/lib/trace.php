<?php

  function pTraceGetLevel ($pad)  {

    if ( $pad === NULL)
      return [];

    if ( $pad < 0 )
      return [];

    if ( ! isset ( $GLOBALS['pad'] ) or $GLOBALS['pad'] < 0 )
      return [];

    if ( is_array($pad) )
      return [];
    
    return [
      'tag' => $GLOBALS ['padTag'] [$pad] ?? '',
      't-type' => $GLOBALS ['padType'] [$pad] ?? '',
      'pair' => $GLOBALS ['padPair'] [$pad] ?? '',
      'p-type' => $GLOBALS ['padPrmsType'] [$pad] ?? '',
      'prm' => $GLOBALS ['padPrm'] [$pad] ?? '',
      'prms' => $GLOBALS ['padPrms'] [$pad] ?? '',
      'flags' => $GLOBALS ['padPrmsTag'] [$pad] ?? '',
      'values' => $GLOBALS ['padPrmsVal'] [$pad] ?? '',
      'true' => pDump_short ($GLOBALS ['padTrue'][$pad]??''),
      'false' => pDump_short ($GLOBALS ['padFalse'][$pad]??''),
      'base' => pDump_short ($GLOBALS ['padBase'][$pad]??''),
      'html' => pDump_short ($GLOBALS ['padHtml'][$pad]??''),
      'result' => pDump_short ($GLOBALS ['padResult'][$pad]??''),
      'name' => $GLOBALS ['padName'] [$pad] ?? '',
      'default' => $GLOBALS ['padDefault'] [$pad] ?? '',
      'walk' => $GLOBALS ['padWalk'] [$pad] ?? '',
      'hit' => $GLOBALS ['padHit'] [$pad] ?? '',
      'null' => $GLOBALS ['padNull'] [$pad] ?? '',
      'else' => $GLOBALS ['padElse'] [$pad] ?? '',
      'array' => $GLOBALS ['padArray'] [$pad] ?? '',
      'text' => $GLOBALS ['padText'] [$pad]?? ''
    ];

  } 


  function pTrace_write_error ($error, $type, $count, $vars, $force=0 ) {

    if ( $GLOBALS ['padError_dump'] and ! $GLOBALS ['padTrace'] )
      return pTrace_write_error_light ($error, $type, $count, $vars);

    if ( ! $force and ! $GLOBALS ['padTrace'] )
      return;

    global $pad, $padOccurDir, $app, $page, $PADREQID;

    $padError_dir = $padOccurDir [$pad] . "/error-$count-$type";

    $data = [];

    $data ['error']   = $error;
    $data ['number']  = $count;
    $data ['dir']     = DATA . $padError_dir;

    foreach ($vars as $key => $val)
      $data [$key] = $val;

    pFile_put_contents ( "errors/$PADREQID-$count-$type.json", $data ); 
    pFile_put_contents ( "$padError_dir/error.json",             $data ); 
    
    pTraceAll ( $padError_dir );

  }


  function pTrace_write_error_light ($error, $type, $count, $vars) {

    global $PADREQID;

    pFile_put_contents ( "errors/$PADREQID-$type-$count.html", pDump_get($error) ); 

  }


  function pTraceAll ( $dir ) {

    pFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

    pFile_put_contents ( "$dir/pad.json",   $padFpad  );
    pFile_put_contents ( "$dir/app.json",   $padFapp  );
    pFile_put_contents ( "$dir/php.json",   $padFphp  );
    pFile_put_contents ( "$dir/levels.json",$padFlvl  );
    pFile_put_contents ( "$dir/ids.json",   $padFids  );
    pFile_put_contents ( "$dir/config.json",$padFcfg  );

  }

?>