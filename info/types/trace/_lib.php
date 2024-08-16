<?php



  function$GLOBALS ['padInfo']( $type, $event, $info='' ) {

    global $pad, $padOccur;
    global $padInfTraceMore, $padInfTraceRoot, $padInfTraceTree, $padInfTraceLocal, $padInfTraceSkipLevel;
    global $padInfTraceId, $padInfTraceTypes, $padInfTraceIds, $padInfTraceOccurId, $padInfTraceMaxLevel;

    if ( $padInfTraceSkipLevel and $padInfTraceSkipLevel == $pad ) return;
    if ( $padInfTraceMaxLevel  and $padInfTraceMaxLevel  >  $pad ) return;

    if ( padTraceSkip ( $type ) )
      return;
    
    $padInfTraceId++;

    $occur = $padOccur [$pad] ?? 0;

    if ( $event == 'start' )
      if     ( $type == 'level' ) $padInfTraceIds [$pad]          = $padInfTraceId;
      elseif ( $type == 'occur' ) $padInfTraceOccurId [$pad]     = $padInfTraceId;
      else                        $GLOBALS ["padTraceX$type"] = $padInfTraceId;

    padTraceInfo ( $trace, $info, $id, $type, $event );

    if ( $padInfTraceMore  ) padTraceMore  ( $trace, $info, $padInfTraceId );
    if ( $padInfTraceRoot  ) padTraceRoot  ( $trace );
    if ( $padInfTraceTree  ) padTraceTree  ( $trace );
    if ( $padInfTraceLocal ) padTraceLocal ( $trace );
    if ( $padInfTraceTypes ) padTraceTypes ( $trace, $type );
   
  }


  function padTraceError ( $error ) {

    if ( ! function_exists ( 'padTrace') )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpToDir ( $error, $GLOBALS ['padTraceDir'] . '/ERROR');
    
    } catch (Throwable $e) {
    
      // Ignore errors

    }

    restore_error_handler ();   

  }


  function padTraceSkip ( $type ) {

    global $pad;
    global $padInfTraceSkipLevel, $padInfTraceMaxLevel;

    if ( $padInfTraceSkipLevel and $padInfTraceSkipLevel == $pad )
      return TRUE;

    if ( $padInfTraceMaxLevel and $padInfTraceMaxLevel > $pad )
      return TRUE;

    return FALSE;

  }


  function padTraceInfo ( &$trace, &$info, &$id, $type, $event ) {

    global $pad, $padOccur;
    global $padInfTraceId, $padInfTraceIds, $padInfTraceOccurId;

    $prefix = $pad;  
    if ( $pad >= 0 and $padOccur [$pad] )
      $prefix .= '/' . $padOccur [$pad];

    if     ( $type == 'level' )                      $id = $padInfTraceIds [$pad]         ?? 0;
    elseif ( $type == 'occur' )                      $id = $padInfTraceOccurId [$pad]     ?? 0;
    elseif ( isset ( $GLOBALS ["padTraceX$type"] ) ) $id = $GLOBALS ["padTraceX$type"] ?? 0;
    else                                             $id = $padInfTraceId;       

    if ( ! $id or $id == $padInfTraceId )
      $id = '';

    $trace = sprintf ( '%-9s',  $prefix       )
           . sprintf ( '%-7s',  $padInfTraceId )
           . sprintf ( '%-7s',  $id           )
           . sprintf ( '%-10s', $type         )
           . sprintf ( '%-10s', $event        );

    $info = padMakeSafe ( $info );

    if ( strlen ( $info ) > 70 ) 
      $trace .= substr ( $info, 0, 63 ) . ' <more>';
    else
      $trace .= $info;

  }


  function padTraceMore ( $trace, $info, $line ) {

    if ( str_ends_with ( $trace, ' <more>' )  )
      padTraceWrite ( -1, "more/$line.txt", $info, 'file' ) ;

  }


  function padTraceRoot ( $trace ) {

    padTraceWrite ( -1, 'root.txt', $trace );

  }


  function padTraceTree ( $trace ) {  

    global $pad;

    for ( $i = 0; $i <= $pad; $i++ )
      padTraceWrite ( $i, 'tree.txt', $trace );

  }

  function padTraceLocal ( $trace ) {  

    global $pad;

    padTraceWrite ( $pad, 'local.txt', $trace );

  }


  function padTraceTypes ( $trace, $type ) {  

    global $pad, $padInfTraceTypesDir;

    if ( $pad > -1 )
      if ( $padInfTraceTypesDir )
        padTraceWrite ( $pad, "/types/$type.txt", $trace ); 
      else
        padTraceWrite ( $pad, "/types-$type.txt", $trace ); 

    if ( $padInfTraceTypesDir )
      padTraceWrite ( -1, "/types/$type.txt", $trace ); 
    else
      padTraceWrite ( -1, "/types-$type.txt", $trace ); 

  }


  function padTraceWrite ( $pad, $location, $trace, $type='line' ) {  

    global $padOccur, $padInfTraceDir;
    global $padInfTraceLevel, $padInfTraceLines, $padInfTraceSkipLevel, $padInfTraceMaxLevel ;

    if ( ! $padInfTraceLines and $type == 'line' )
      return;

    if ( $padInfTraceSkipLevel and $padInfTraceSkipLevel == $pad ) return;
    if ( $padInfTraceMaxLevel  and $padInfTraceMaxLevel  >  $pad ) return;

    if ( ! isset ( $padInfTraceLevel [$pad] ) ) padTraceSet ( $pad );
    if ( ! $padInfTraceLevel [$pad]           ) padTraceSet ( $pad );

    if ( $pad < 0 ) 
      $add = '';
    else
      $add = $padInfTraceLevel [$pad] . '/' . padTraceOccur ( $pad );

    $target = "$padInfTraceDir/$add$location";

    if ( $type == 'file' and file_exists ( '/data/' . $target ) )
      return;

    if ( $type == 'line' )
      padInfoLine ( $target, $trace );
    else
      padInfoFile ( $target, $trace );

  }

 
  function padTraceCheckDir ( $file, $type ) {

    if ( $type == 'file' )
      return;

    padInfoChkDir ( $file );    

  }


  function padTraceOccur ( $pad ) {  

    global $padOccur;
    global $padInfTraceOccurs, $padInfTraceOccursSmart, $padInfTraceInitsExits, $padInfTraceDefault, $padInfTraceHideDefault;

    if ( $pad < 0 )
      return '';

    $occur = $padOccur [$pad] ?? 0;

    if     ( $padInfTraceInitsExits and $occur == 0     ) return 'inits/';
    elseif ( $padInfTraceInitsExits and $occur == 99999 ) return 'exits/';

    if ( ! $padInfTraceOccurs      ) return '';
    if ( ! $padInfTraceOccursSmart ) return "$occur/";

    if     ( $occur == 0              ) return '';
    elseif ( $occur == 99999          ) return '';
    elseif ( padTraceDefault ( $pad ) ) return '';
    else                                return "$occur/";

  }



  function padTraceDeleteDir( $dir ) {

    if ( ! file_exists ( $dir ) )
      return;
    
    $files = glob($dir . '*', GLOB_MARK);
  
    foreach ($files as $file)
        if (is_dir($file))
           padTraceDeleteDir($file);
        else
            unlink($file);
  
    rmdir($dir);

  }


  function padTraceDefault ( $pad ) {

    global $padWalk, $padData, $padBeforeBase, $padAfterBase, $padStartBase, $padEndBase;

    if ( $pad == 0 )
      return TRUE;

    if ( ! isset ( $padWalk [$pad] ) or ! isset ( $padData [$pad] ) )
      return FALSE;

    if ( ! isset ( $padStartBase [$pad] ) or ! isset ( $padEndBase [$pad] ) )
      return FALSE;

    if ( ! isset ( $padBeforeBase [$pad] ) or ! isset ( $padAfterBase [$pad] ) )
      return FALSE;

    if ( $padWalk [$pad] == 'next'      
      or count ( $padData [$pad] ) > 1   
      or $padBeforeBase [$pad]           
      or $padAfterBase  [$pad] 
      or $padStartBase  [$pad]           
      or $padEndBase    [$pad] )

      return FALSE;

    return TRUE;

  }


  function padTraceSet ( $pad ) {  

    global $padOccur, $padTag;
    global $padInfTraceLevel, $padInfTraceType, $padInfTraceAddLine;
    global $padInfTraceIds, $padInfTraceId, $padInfTraceLevelChilds, $padInfTraceOccurChilds;

    if ( $pad < 0 ) {
      $padInfTraceLevel [$pad] = '';
      return;
    }
    
    $last = $pad - 1;
    $tag  = $padTag [$pad] ?? "NoTag";
    $add  = padTraceOccur ( $pad-1 );
    $line = ($padInfTraceAddLine) ? "$padInfTraceId-" : '';

    $padInfTraceLevel [$pad] = $padInfTraceLevel [$last] ?? "NoPrev";
 
    $padInfTraceLevel [$pad] .= "/$add$line$tag";
  
    $padInfTraceLevelChilds [$pad] = 0;
    $padInfTraceOccurChilds [$pad] = [];

    $padInfTraceIds [$pad] = $padInfTraceId;

  }


  function padTraceStatus ( ) {
  
    global $pad, $padNull, $padHit, $padElse, $padData, $padInfTraceLevel;

    if     ( $padNull [$pad] ) $status = padTraceStatusGo ( 'null'  );
    elseif ( $padHit  [$pad] ) $status = padTraceStatusGo ( 'hit'   );
    elseif ( $padElse [$pad] ) $status = padTraceStatusGo ( 'else'  );
    else                       $status = padTraceStatusGo ( 'other' );

    if ( ! padIsDefaultData ( $padData [$pad] ) and count ( $padData [$pad] ) ) 
      $status .= '-' . count ( $padData [$pad] ) ;

    padTraceWrite ( $pad, "status-$status.txt", $status, 'file' );
   
  }


  function padTraceStatusGo ( $type ) {

    global $pad, $padResult, $padBase, $padBase, $padFalse;

    if ( $padResult [$pad] and $padBase [$pad] )
      if     ( $padBase [$pad] == $padBase  [$pad] )     return $type . '-true';
      elseif ( $padBase [$pad] == $padFalse )            return $type . '-else';

    if     ( ! $padResult [$pad] and ! $padBase [$pad] ) return $type . '-no-base';
    elseif ( $padResult [$pad]                         ) return $type . '-result';
    else                                                 return $type;

  }


  function padTraceChilds ( $dir, &$childs, $type ) {
  
    global $pad, $padOccur, $padInfTraceLevel, $padInfTraceDir;

    if ( ! $dir or ! $childs )
      return;

    $dir = "$padInfTraceDir/$dir";

    $new  = $dir . '-' . $childs;
    $from = '/data/' . $dir;
    $to   = '/data/' . $new;

    if ( ! file_exists ( $from ) or file_exists ( $to) )
      return;

    rename ( $from, $to );

  }


  function padTraceCheckLocal ( $dir ) {

    global $pad, $padInfTraceDir;

    $file1 = '/data/' . "$padInfTraceDir/$dir/trace.txt";
    $file2 = '/data/' . "$padInfTraceDir/$dir/local.txt";

    if ( ! file_exists ( $file1 ) or ! file_exists ( $file2 ) )
      return;

    if ( filesize ( $file1 ) == filesize ( $file2 ) )
      unlink ( $file2 );

  }


?>