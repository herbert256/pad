<?php


  function padDefaultOccur ( ) {

    global $pad, $padWalk, $padData;

    if ( $padWalk [$pad] <> 'next' and padIsDefaultData ( $padData [$pad] ) )
      return TRUE;

    return FALSE;


  }

  function padTraceStatus ( $dir ) {
  
    global $pad, $padNull, $padHit, $padElse, $padData;

    if     ( $padNull [$pad] ) $status = padTraceStatusGo ( 'null'  );
    elseif ( $padHit  [$pad] ) $status = padTraceStatusGo ( 'hit'   );
    elseif ( $padElse [$pad] ) $status = padTraceStatusGo ( 'else'  );
    else                       $status = padTraceStatusGo ( 'other' );

    if ( ! padIsDefaultData ( $padData [$pad] ) and count ( $padData [$pad] ) ) 
      $status .= '-' . count ( $padData [$pad] ) ;

    touch ( padData . "$dir/status.$status.txt" );
   
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
  
    global $pad, $padOccur, $padTraceLevel, $padTraceOccur;

    if ( ! $dir or ! $childs )
      return;

    $new  = $dir . '-' . $childs;
    $from = padData . $dir;
    $to   = padData . $new;

    if ( ! file_exists ( $from ) or file_exists ( $to) )
      return;

    rename ( $from, $to );

    if ( $type == 'level' ) {
      $padTraceLevel [$pad]     = $new;
      $padTraceOccur [$pad] [0] = "$new/0";
    }

    if ( $type == 'occur' ) {
      $occur = $padOccur [$pad] ?? 0;
      $padTraceOccur [$pad] [$occur] = $new;
    }

    $childs = 0;

  }


  function padTraceCheckLocal ( $dir ) {

    global $pad;

    $file1 = padData . $dir . '/trace.txt';
    $file2 = padData . $dir . '/local.txt';

    if ( ! file_exists ( $file1 ) or ! file_exists ( $file2 ) )
      return;

    if ( filesize ( $file1 ) == filesize ( $file2 ) )
      unlink ( $file2 );

  }


?>