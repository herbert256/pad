<?php


  function padTraceSet ( $pad ) {  

    global $padOccur, $padTag;
    global $padTraceLevel, $padTraceGo, $padTraceType;
    global $padTraceLine, $padTraceLevelChilds, $padTraceOccurChilds;

    if ( $pad < 0 ) {
      $padTraceLevel [$pad] = '';
      return;
    }
    
    $last  = $pad - 1;
    $occur = $padOccur [$last] ?? 0;
    $tag   = $padTag [$pad] ?? "NoTag-$pad";

    $add = ( $pad > 0 ) ? "/$occur" : '';

    $padTraceLevel [$pad]  = $padTraceLevel [$last] ?? "NoPrev-$pad";
    $padTraceLevel [$pad] .= "$add/$padTraceLine-" . padFileCorrect ( $tag );
  
    $padTraceLevelChilds [$pad] = 0;
    $padTraceOccurChilds [$pad] = [];

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