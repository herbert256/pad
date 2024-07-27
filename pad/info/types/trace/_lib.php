<?php



  function padTrace ( $type, $event, $info='' ) {

    global $pad, $padOccur;
    global $padTraceMore, $padTraceRoot, $padTraceTree, $padTraceLocal, $padTraceSkipLevel;
    global $padTraceId, $padTraceTypes, $padTraceIds, $padTraceOccurId, $padTraceMaxLevel;

    if ( $padTraceSkipLevel and $padTraceSkipLevel == $pad ) return;
    if ( $padTraceMaxLevel  and $padTraceMaxLevel  >  $pad ) return;

    if ( padTraceSkip ( $type ) )
      return;
    
    $padTraceId++;

    $occur = $padOccur [$pad] ?? 0;

    if ( $event == 'start' )
      if     ( $type == 'level' ) $padTraceIds [$pad]          = $padTraceId;
      elseif ( $type == 'occur' ) $padTraceOccurId [$pad]     = $padTraceId;
      else                        $GLOBALS ["padTraceX$type"] = $padTraceId;

    padTraceInfo ( $trace, $info, $id, $type, $event );

    if ( $padTraceMore  ) padTraceMore  ( $trace, $info, $padTraceId );
    if ( $padTraceRoot  ) padTraceRoot  ( $trace );
    if ( $padTraceTree  ) padTraceTree  ( $trace );
    if ( $padTraceLocal ) padTraceLocal ( $trace );
    if ( $padTraceTypes ) padTraceTypes ( $trace, $type );
   
  }


  function padTraceError ( $error ) {

    if ( ! function_exists ( 'padTrace') )
      return;

    set_error_handler ( 'padThrow' );

    try {

      padDumpToDir ( $error, $GLOBALS ['padTraceDir'] . '/ERROR');
    
    } catch (Throwable $e) {
    
      // Ignore errors

    }

    restore_error_handler ();   

  }


  function padTraceSkip ( $type ) {

    global $pad;
    global $padTraceSkipLevel, $padTraceMaxLevel;

    if ( $padTraceSkipLevel and $padTraceSkipLevel == $pad )
      return TRUE;

    if ( $padTraceMaxLevel and $padTraceMaxLevel > $pad )
      return TRUE;

    return FALSE;

  }


  function padTraceInfo ( &$trace, &$info, &$id, $type, $event ) {

    global $pad, $padOccur;
    global $padTraceId, $padTraceIds, $padTraceOccurId;

    $prefix = $pad;  
    if ( $pad >= 0 and $padOccur [$pad] )
      $prefix .= '/' . $padOccur [$pad];

    if     ( $type == 'level' )                      $id = $padTraceIds [$pad]         ?? 0;
    elseif ( $type == 'occur' )                      $id = $padTraceOccurId [$pad]     ?? 0;
    elseif ( isset ( $GLOBALS ["padTraceX$type"] ) ) $id = $GLOBALS ["padTraceX$type"] ?? 0;
    else                                             $id = $padTraceId;       

    if ( ! $id or $id == $padTraceId )
      $id = '';

    $trace = sprintf ( '%-9s',  $prefix       )
           . sprintf ( '%-7s',  $padTraceId )
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

    global $pad, $padTraceTypesDir;

    if ( $pad > -1 )
      if ( $padTraceTypesDir )
        padTraceWrite ( $pad, "/types/$type.txt", $trace ); 
      else
        padTraceWrite ( $pad, "/types-$type.txt", $trace ); 

    if ( $padTraceTypesDir )
      padTraceWrite ( -1, "/types/$type.txt", $trace ); 
    else
      padTraceWrite ( -1, "/types-$type.txt", $trace ); 

  }


  function padTraceWrite ( $pad, $location, $trace, $type='line' ) {  

    global $padOccur, $padTraceDir;
    global $padTraceLevel, $padTraceLines, $padTraceSkipLevel, $padTraceMaxLevel ;

    if ( ! $padTraceLines and $type == 'line' )
      return;

    if ( $padTraceSkipLevel and $padTraceSkipLevel == $pad ) return;
    if ( $padTraceMaxLevel  and $padTraceMaxLevel  >  $pad ) return;

    if ( ! isset ( $padTraceLevel [$pad] ) ) padTraceSet ( $pad );
    if ( ! $padTraceLevel [$pad]           ) padTraceSet ( $pad );

    if ( $pad < 0 ) 
      $add = '';
    else
      $add = $padTraceLevel [$pad] . '/' . padTraceOccur ( $pad );

    $target = "$padTraceDir/$add$location";

    if ( $type == 'file' and file_exists ( padData . $target ) )
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
    global $padTraceOccurs, $padTraceOccursSmart, $padTraceInitsExits, $padTraceDefault, $padTraceHideDefault;

    if ( $pad < 0 )
      return '';

    $occur = $padOccur [$pad] ?? 0;

    if     ( $padTraceInitsExits and $occur == 0     ) return 'inits/';
    elseif ( $padTraceInitsExits and $occur == 99999 ) return 'exits/';

    if ( ! $padTraceOccurs      ) return '';
    if ( ! $padTraceOccursSmart ) return "$occur/";

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
    global $padTraceLevel, $padTraceType, $padTraceAddLine;
    global $padTraceIds, $padTraceId, $padTraceLevelChilds, $padTraceOccurChilds;

    if ( $pad < 0 ) {
      $padTraceLevel [$pad] = '';
      return;
    }
    
    $last = $pad - 1;
    $tag  = $padTag [$pad] ?? "NoTag";
    $add  = padTraceOccur ( $pad-1 );
    $line = ($padTraceAddLine) ? "$padTraceId-" : '';

    $padTraceLevel [$pad] = $padTraceLevel [$last] ?? "NoPrev";
 
    $padTraceLevel [$pad] .= "/$add$line$tag";
  
    $padTraceLevelChilds [$pad] = 0;
    $padTraceOccurChilds [$pad] = [];

    $padTraceIds [$pad] = $padTraceId;

  }


  function padTraceStatus ( ) {
  
    global $pad, $padNull, $padHit, $padElse, $padData, $padTraceLevel;

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
  
    global $pad, $padOccur, $padTraceLevel, $padTraceDir;

    if ( ! $dir or ! $childs )
      return;

    $dir = "$padTraceDir/$dir";

    $new  = $dir . '-' . $childs;
    $from = padData . $dir;
    $to   = padData . $new;

    if ( ! file_exists ( $from ) or file_exists ( $to) )
      return;

    rename ( $from, $to );

  }


  function padTraceCheckLocal ( $dir ) {

    global $pad, $padTraceDir;

    $file1 = padData . "$padTraceDir/$dir/trace.txt";
    $file2 = padData . "$padTraceDir/$dir/local.txt";

    if ( ! file_exists ( $file1 ) or ! file_exists ( $file2 ) )
      return;

    if ( filesize ( $file1 ) == filesize ( $file2 ) )
      unlink ( $file2 );

  }


?>