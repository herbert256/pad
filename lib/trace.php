<?php

  function padTrace ( $dir, $trace='' ) {

    if ( $trace ) {
      $data ['trace'] = $trace;
      padFilePutContents ( "$dir/trace.json", $data ) ; 
    }

    padTraceFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

    padFilePutContents ( "$dir/pad.json",    $padFpad );
    padFilePutContents ( "$dir/app.json",    $padFapp );
    padFilePutContents ( "$dir/php.json",    $padFphp );
    padFilePutContents ( "$dir/level.json",  $padFlvl );
    padFilePutContents ( "$dir/ids.json",    $padFids );
    padFilePutContents ( "$dir/config.json", $padFcfg );
    padFilePutContents ( "$dir/stack.json",  padTraceStack () ) ;

  }

  function padTraceStack () {

    $stack = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);

    foreach ( $stack as $key => $one ) {
      extract ( $one );
      $trace [] = "$file:$line - $function";
    }

    return $trace;
    
  }

  function padTraceGetLevel ($pad)  {

    if ( $pad === NULL or $pad < 0 or ! isset($pad) )
      return [];
    

    return [
      'tag'    => $GLOBALS ['padTag'] [$pad] ?? '',
      't-type' => $GLOBALS ['padType'] [$pad] ?? '',
      'pair'   => $GLOBALS ['padPair'] [$pad] ?? '',
      'p-type' => $GLOBALS ['padPrmType'] [$pad] ?? '',
      'opt'    => $GLOBALS ['padOpt'] [$pad] ?? '',
      'prm'    => $GLOBALS ['padPrm'] [$pad] ?? '',
      'set'    => $GLOBALS ['padSet'] [$pad] ?? '',
      'true' => padDumpShort ($GLOBALS ['padTrue'][$pad]??''),
      'false' => padDumpShort ($GLOBALS ['padFalse'][$pad]??''),
      'base' => padDumpShort ($GLOBALS ['padBase'][$pad]??''),
      'html' => padDumpShort ($GLOBALS ['padHtml'][$pad]??''),
      'result' => padDumpShort ($GLOBALS ['padResult'][$pad]??''),
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

  function padTraceFields ( &$php, &$lvl, &$app, &$cfg, &$pad, &$ids ) {

    $php = $lvl = $app = $cfg = $pad = $ids = [];

    $not  = [ 'GLOBALS', 'padFphp', 'padFlvl', 'padFapp', 'padFcfg', 'padFpad', 'padFids'  ];

    $chk1 = [ '_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];

    $chk2 = [ 'padTag','padType','padPair','padTrue','padFalse','padPrm','padName','padData','padCurrent','padKey','padDefault','padWalk','padWalkData','padDone','padOccur','padStart','padEnd','padBase','padHtml','padResult','padHit','padNull','padElse','padArray','padText','padLevelDir','padOccurDir','padSaveVars','padDeleteVars','padSetSave','padSetDelete','padTagCnt', 'padAfter', 'padBefore', 'padBeforeData', 'padEndOptions', 'padPrmType', 'padSet', 'padGiven'];

    $chk3 = [ 'page','app','PADSESSID','PADREQID','PHPSESSID','PADREFID' ];

    $settings = padFileGetContents(PAD . 'config/config.php');

    foreach ($GLOBALS as $key => $value) {

      if ( ! in_array ($key, $not) ) {

        if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t"))

          $cfg  [$key] = $value;

        elseif ( in_array ( $key, $chk3 ) )
          
          $ids [$key] = $value;

        elseif ( in_array ( $key, $chk1 ) )
          
          $php [$key] = $value;

        elseif ( in_array ( $key, $chk2 ) ) {

          if ( isset($value[0]) and ! $value[0] )
            unset ($value[0]);
          
          $lvl [$key] = $value;
   
        } elseif ( substr($key, 0, 3)  == 'pad' )

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


?>