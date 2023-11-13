<?php


  function padTraceDdeleteDir( $dir ) {

    if ( ! file_exists ( $dir ) )
      return;
    
    $files = glob($dir . '*', GLOB_MARK);
  
    foreach ($files as $file)
        if (is_dir($file))
           padTraceDdeleteDir($file);
        else
            unlink($file);
  
    rmdir($dir);

  }


  function padTraceDefault ( $pad ) {

    global $padWalk, $padData, $padBeforeBase, $padAfterBase;

    if ( $pad == 0 )
      return TRUE;

    if ( ! isset ( $padWalk [$pad] ) or ! isset ( $padData [$pad] ) )
      return FALSE;

    if ( ! isset ( $padBeforeBase [$pad] ) or ! isset ( $padAfterBase [$pad] ) )
      return FALSE;

    if ( $padWalk [$pad] == 'next'      
      or count ( $padData [$pad] ) > 1   
      or $padBeforeBase [$pad]           
      or $padAfterBase  [$pad] )

      return FALSE;

    return TRUE;

  }


  function padTraceSet ( $pad ) {  

    global $padOccur, $padTag;
    global $padTraceLevel, $padTraceGo, $padTraceType, $padTraceAddLine;
    global $padTraceId, $padTraceLine, $padTraceLevelChilds, $padTraceOccurChilds;

    if ( $pad < 0 ) {
      $padTraceLevel [$pad] = '';
      return;
    }
    
    $last = $pad - 1;
    $tag  = $padTag [$pad] ?? "NoTag";
    $add  = padTraceOccur ( $pad-1 );
    $line = ($padTraceAddLine) ? "$padTraceLine-" : '';

    $padTraceLevel [$pad] = $padTraceLevel [$last] ?? "NoPrev";

    if ( $padTraceLevel [$pad] == '*SKIP*' )
      $padTraceLevel [$pad] = '';
 
    $padTraceLevel [$pad] .= "/$add$line" . padFileCorrect ( $tag );
  
    $padTraceLevelChilds [$pad] = 0;
    $padTraceOccurChilds [$pad] = [];

    $padTraceId [$pad] = $padTraceLine;

  }


  function padTraceStatus ( ) {
  
    global $padTraceActive, $pad, $padNull, $padHit, $padElse, $padData, $padTraceLevel;

    if     ( $padNull [$pad] ) $status = padTraceStatusGo ( 'null'  );
    elseif ( $padHit  [$pad] ) $status = padTraceStatusGo ( 'hit'   );
    elseif ( $padElse [$pad] ) $status = padTraceStatusGo ( 'else'  );
    else                       $status = padTraceStatusGo ( 'other' );

    if ( ! padIsDefaultData ( $padData [$pad] ) and count ( $padData [$pad] ) ) 
      $status .= '-' . count ( $padData [$pad] ) ;

    $padTraceActive = FALSE;
    padTraceWrite ( $pad, "status-$status.txt", $status, 'file' );
    $padTraceActive = TRUE;
   
  }


  function padTraceStatusGo ( $type ) {

    global $pad, $padResult, $padBase, $padTrue, $padFalse;

    if ( $padResult [$pad] and $padBase [$pad] )
      if     ( $padBase [$pad] == $padTrue  [$pad] )     return $type . '-true';
      elseif ( $padBase [$pad] == $padFalse [$pad] )     return $type . '-else';

    if     ( ! $padResult [$pad] and ! $padBase [$pad] ) return $type . '-no-base';
    elseif ( $padResult [$pad]                         ) return $type . '-result';
    else                                                 return $type;

  }

  function padTraceDump ( $type ) {

    global $pad;
    global $padTraceActive, $padTraceDumps, $padTraceType, $padTraceBase, $padTraceLevel;

    $padTraceActive = FALSE;

    if ( $padTraceType == 'config' )
      padDumpToDir ( '', $padTraceBase . "/$type" );
    else
      padDumpToDir ( '', $padTraceLevel [$pad] . "/$type" );
  
    $padTraceActive = TRUE;

  }



  function padTraceChilds ( $dir, &$childs, $type ) {
  
    global $pad, $padOccur, $padTraceLevel, $padTraceBase;

    if ( ! $dir or ! $childs )
      return;

    $dir = "$padTraceBase/$dir";

    $new  = $dir . '-' . $childs;
    $from = padData . $dir;
    $to   = padData . $new;

    if ( ! file_exists ( $from ) or file_exists ( $to) )
      return;

    rename ( $from, $to );

  }


  function padTraceCheckLocal ( $dir ) {

    global $pad, $padTraceBase;

    $file1 = padData . "$padTraceBase/$dir/trace.txt";
    $file2 = padData . "$padTraceBase/$dir/local.txt";

    if ( ! file_exists ( $file1 ) or ! file_exists ( $file2 ) )
      return;

    if ( filesize ( $file1 ) == filesize ( $file2 ) )
      unlink ( $file2 );

  }


?>