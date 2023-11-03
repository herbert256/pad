<?php


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


  function padTraceChilds ( $dir, $childs ) {
  
    if ( ! $dir or ! $childs )
      return;

    rename ( padData . $dir, padData . $dir . '-' . $childs );

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