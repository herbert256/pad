<?php


  function padInfoTrace ( $type, $event, $info='' ) {

    global $pad, $padOccur;
    global $padInfoTraceMore, $padInfoTraceRoot, $padInfoTraceTree, $padInfoTraceLocal, $padInfoTraceSkipLevel;
    global $padInfoTraceId, $padInfoTraceTypes, $padInfoTraceIds, $padInfoTraceOccurId, $padInfoTraceMaxLevel;

    if ( $padInfoTraceSkipLevel and $padInfoTraceSkipLevel == $pad ) return;
    if ( $padInfoTraceMaxLevel  and $padInfoTraceMaxLevel  >  $pad ) return;

    if ( padInfoTraceSkip ( $type ) )
      return;
    
    $padInfoTraceId++;

    $occur = $padOccur [$pad] ?? 0;

    if ( $event == 'start' )
      if     ( $type == 'level' ) $padInfoTraceIds [$pad]         = $padInfoTraceId;
      elseif ( $type == 'occur' ) $padInfoTraceOccurId [$pad]     = $padInfoTraceId;
      else                        $GLOBALS ["padInfoTraceX$type"] = $padInfoTraceId;

    padInfoTraceInfo ( $trace, $info, $id, $type, $event );

    if ( $padInfoTraceMore  ) padInfoTraceMore  ( $trace, $info, $padInfoTraceId );
    if ( $padInfoTraceRoot  ) padInfoTraceRoot  ( $trace );
    if ( $padInfoTraceTree  ) padInfoTraceTree  ( $trace );
    if ( $padInfoTraceLocal ) padInfoTraceLocal ( $trace );
    if ( $padInfoTraceTypes ) padInfoTraceTypes ( $trace, $type );
   
  }


  function padInfoTraceError ( $error ) {

    if ( ! function_exists ( 'padInfoTrace') )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpToDir ( $error, $GLOBALS ['padInfoTraceDir'] . '/ERROR');
    
    } catch (Throwable $e) {
    
      // Ignore errors

    }

    restore_error_handler ();   

  }


  function padInfoTraceSkip ( $type ) {

    global $pad;
    global $padInfoTraceSkipLevel, $padInfoTraceMaxLevel;

    if ( $padInfoTraceSkipLevel and $padInfoTraceSkipLevel == $pad )
      return TRUE;

    if ( $padInfoTraceMaxLevel and $padInfoTraceMaxLevel > $pad )
      return TRUE;

    return FALSE;

  }


  function padInfoTraceInfo ( &$trace, &$info, &$id, $type, $event ) {

    global $pad, $padOccur;
    global $padInfoTraceId, $padInfoTraceIds, $padInfoTraceOccurId;

    $prefix = $pad;  
    if ( $pad >= 0 and $padOccur [$pad] )
      $prefix .= '/' . $padOccur [$pad];

    if     ( $type == 'level' )                      $id = $padInfoTraceIds [$pad]         ?? 0;
    elseif ( $type == 'occur' )                      $id = $padInfoTraceOccurId [$pad]     ?? 0;
    elseif ( isset ( $GLOBALS ["padInfoTraceX$type"] ) ) $id = $GLOBALS ["padInfoTraceX$type"] ?? 0;
    else                                             $id = $padInfoTraceId;       

    if ( ! $id or $id == $padInfoTraceId )
      $id = '';

    $trace = sprintf ( '%-9s',  $prefix       )
           . sprintf ( '%-7s',  $padInfoTraceId )
           . sprintf ( '%-7s',  $id           )
           . sprintf ( '%-10s', $type         )
           . sprintf ( '%-10s', $event        );

    $info = padMakeSafe ( $info );

    if ( strlen ( $info ) > 70 ) 
      $trace .= substr ( $info, 0, 63 ) . ' <more>';
    else
      $trace .= $info;

  }


  function padInfoTraceMore ( $trace, $info, $line ) {

    if ( str_ends_with ( $trace, ' <more>' )  )
      padInfoTraceWrite ( -1, "more/$line.txt", $info, 'file' ) ;

  }


  function padInfoTraceRoot ( $trace ) {

    padInfoTraceWrite ( -1, 'root.txt', $trace );

  }


  function padInfoTraceTree ( $trace ) {  

    global $pad;

    for ( $i = 0; $i <= $pad; $i++ )
      padInfoTraceWrite ( $i, 'tree.txt', $trace );

  }

  function padInfoTraceLocal ( $trace ) {  

    global $pad;

    padInfoTraceWrite ( $pad, 'local.txt', $trace );

  }


  function padInfoTraceTypes ( $trace, $type ) {  

    global $pad, $padInfoTraceTypesDir;

    if ( $pad > -1 )
      if ( $padInfoTraceTypesDir )
        padInfoTraceWrite ( $pad, "/types/$type.txt", $trace ); 
      else
        padInfoTraceWrite ( $pad, "/types-$type.txt", $trace ); 

    if ( $padInfoTraceTypesDir )
      padInfoTraceWrite ( -1, "/types/$type.txt", $trace ); 
    else
      padInfoTraceWrite ( -1, "/types-$type.txt", $trace ); 

  }


  function padInfoTraceWrite ( $pad, $location, $trace, $type='line' ) {  

    global $padOccur, $padInfoTraceDir;
    global $padInfoTraceLevel, $padInfoTraceLines, $padInfoTraceSkipLevel, $padInfoTraceMaxLevel ;

    if ( ! $padInfoTraceLines and $type == 'line' )
      return;

    if ( $padInfoTraceSkipLevel and $padInfoTraceSkipLevel == $pad ) return;
    if ( $padInfoTraceMaxLevel  and $padInfoTraceMaxLevel  >  $pad ) return;

    if ( ! isset ( $padInfoTraceLevel [$pad] ) ) padInfoTraceSet ( $pad );
    if ( ! $padInfoTraceLevel [$pad]           ) padInfoTraceSet ( $pad );

    if ( $pad < 0 ) 
      $add = '';
    else
      $add = $padInfoTraceLevel [$pad] . '/' . padInfoTraceOccur ( $pad );

    $target = "$padInfoTraceDir/$add$location";

    if ( $type == 'file' and file_exists ( '/data/' . $target ) )
      return;

    if ( $type == 'line' )
      padInfoLine ( $target, $trace );
    else
      padInfoFile ( $target, $trace );

  }

 
  function padInfoTraceCheckDir ( $file, $type ) {

    if ( $type == 'file' )
      return;

    padInfoChkDir ( $file );    

  }


  function padInfoTraceOccur ( $pad ) {  

    global $padOccur;
    global $padInfoTraceOccurs, $padInfoTraceOccursSmart, $padInfoTraceInitsExits, $padInfoTraceDefault, $padInfoTraceHideDefault;

    if ( $pad < 0 )
      return '';

    $occur = $padOccur [$pad] ?? 0;

    if     ( $padInfoTraceInitsExits and $occur == 0     ) return 'inits/';
    elseif ( $padInfoTraceInitsExits and $occur == 99999 ) return 'exits/';

    if ( ! $padInfoTraceOccurs      ) return '';
    if ( ! $padInfoTraceOccursSmart ) return "$occur/";

    if     ( $occur == 0              ) return '';
    elseif ( $occur == 99999          ) return '';
    elseif ( padInfoTraceDefault ( $pad ) ) return '';
    else                                return "$occur/";

  }



  function padInfoTraceDeleteDir( $dir ) {

    if ( ! file_exists ( $dir ) )
      return;
    
    $files = glob($dir . '*', GLOB_MARK);
  
    foreach ($files as $file)
        if (is_dir($file))
           padInfoTraceDeleteDir($file);
        else
            unlink($file);
  
    rmdir($dir);

  }


  function padInfoTraceDefault ( $pad ) {

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


  function padInfoTraceSet ( $pad ) {  

    global $padOccur, $padTag;
    global $padInfoTraceLevel, $padInfoTraceType, $padInfoTraceAddLine;
    global $padInfoTraceIds, $padInfoTraceId, $padInfoTraceLevelChilds, $padInfoTraceOccurChilds;

    if ( $pad < 0 ) {
      $padInfoTraceLevel [$pad] = '';
      return;
    }
    
    $last = $pad - 1;
    $tag  = $padTag [$pad] ?? "NoTag";
    $add  = padInfoTraceOccur ( $pad-1 );
    $line = ($padInfoTraceAddLine) ? "$padInfoTraceId-" : '';

    $padInfoTraceLevel [$pad] = $padInfoTraceLevel [$last] ?? "NoPrev";
 
    $padInfoTraceLevel [$pad] .= "/$add$line$tag";
  
    $padInfoTraceLevelChilds [$pad] = 0;
    $padInfoTraceOccurChilds [$pad] = [];

    $padInfoTraceIds [$pad] = $padInfoTraceId;

  }


  function padInfoTraceStatus ( ) {
  
    global $pad, $padNull, $padHit, $padElse, $padData, $padInfoTraceLevel;

    if     ( $padNull [$pad] ) $status = padInfoTraceStatusGo ( 'null'  );
    elseif ( $padHit  [$pad] ) $status = padInfoTraceStatusGo ( 'hit'   );
    elseif ( $padElse [$pad] ) $status = padInfoTraceStatusGo ( 'else'  );
    else                       $status = padInfoTraceStatusGo ( 'other' );

    if ( ! padIsDefaultData ( $padData [$pad] ) and count ( $padData [$pad] ) ) 
      $status .= '-' . count ( $padData [$pad] ) ;

    padInfoTraceWrite ( $pad, "status-$status.txt", $status, 'file' );
   
  }


  function padInfoTraceStatusGo ( $type ) {

    global $pad, $padResult, $padBase, $padBase, $padFalse;

    if ( $padResult [$pad] and $padBase [$pad] )
      if     ( $padBase [$pad] == $padBase  [$pad] )     return $type . '-true';
      elseif ( $padBase [$pad] == $padFalse )            return $type . '-else';

    if     ( ! $padResult [$pad] and ! $padBase [$pad] ) return $type . '-no-base';
    elseif ( $padResult [$pad]                         ) return $type . '-result';
    else                                                 return $type;

  }


  function padInfoTraceChilds ( $dir, &$childs, $type ) {
  
    global $pad, $padOccur, $padInfoTraceLevel, $padInfoTraceDir;

    if ( ! $dir or ! $childs )
      return;

    $dir = "$padInfoTraceDir/$dir";

    $new  = $dir . '-' . $childs;
    $from = '/data/' . $dir;
    $to   = '/data/' . $new;

    if ( ! file_exists ( $from ) or file_exists ( $to) )
      return;

    rename ( $from, $to );

  }


  function padInfoTraceCheckLocal ( $dir ) {

    global $pad, $padInfoTraceDir;

    $file1 = "/data/$padInfoTraceDir/$dir/tree.txt";
    $file2 = "/data/$padInfoTraceDir/$dir/local.txt";

    if ( ! file_exists ( $file1 ) or ! file_exists ( $file2 ) )
      return;

    if ( filesize ( $file1 ) == filesize ( $file2 ) )
      unlink ( $file2 );

  }


?>