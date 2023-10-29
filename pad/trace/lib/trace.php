<?php


  function padTrace ( $type, $event, $info='' ) {

    global $pad, $padOccur, $padTag, $padWalk, $padData;
    global $padTrace, $padTraceId, $padTraceTypes, $padTraceDirBase, $padTraceDir, $padTraceStart, $padTraceActive;
 
    $padTraceActive = FALSE;

    $padTrace++;

    if ( $event == 'start' ) {
      if     ( $type == 'level' ) $GLOBALS ['padTraceId']    [$pad] = $padTrace;
      elseif ( $type == 'occur' ) $GLOBALS ['padTraceOccur'] [$pad] = $padTrace;
      else                        $GLOBALS ["padTraceX$type"]       = $padTrace;
    }

    $prefix = $pad;  
    
    if ( isset ( $padOccur ) and isset ( $padOccur [$pad] ) and $padOccur [$pad] )
      $prefix .= '/' . $padOccur [$pad];

    if     ( $type == 'level' )                      $id = $GLOBALS ['padTraceId']    [$pad] ?? 0;
    elseif ( $type == 'occur' )                      $id = $GLOBALS ['padTraceOccur'] [$pad] ?? 0;
    elseif ( isset ( $GLOBALS ["padTraceX$type"] ) ) $id = $GLOBALS ["padTraceX$type"]       ?? 0;
    else                                             $id = $padTrace;       

    if ( $id == $padTrace )
      $id = '';

    $trace = sprintf ( '%-7s',  $prefix   )
           . sprintf ( '%-7s',  $padTrace )
           . sprintf ( '%-7s',  $id       )
           . sprintf ( '%-10s', $type     )
           . sprintf ( '%-10s', $event    )
           . padMakeSafe ( $info, 80 );  

    $padTraceDir = $padTraceDirBase;

    padTraceTreeGo ( $padTraceDir, $type, $trace );

    for ( $i = $padTraceStart; $i <= $pad; $i++ ) {

      if ( $i == 0 )
        $padTraceDir .= '/page' ;
      else
        $padTraceDir .= '/' . $padTraceId [$i] . '-' .  padFileCorrect ( $padTag [$i] );

      padTraceTreeGo ( $padTraceDir, $type, $trace );
    
      if ( $padOccur [$i] and $padTraceTypes ['occur'] ) {

        if ( $padWalk [$i] <> 'next' and padIsDefaultData ( $padData [$i] ) )
          continue;

        $padTraceDir .= '/' . $padOccur [$i];
        padTraceTreeGo ( $padTraceDir, $type, $trace );
 
      }
  
    }

    if ( $padTraceTypes ['local'] )
      padFilePutContents ( "$padTraceDir/local.txt", $trace, true );

    $padTraceActive = TRUE;

  }


  function padTraceTreeGo ( $location, $type, $trace ) {  

    padFilePutContents  ( "$location/trace.txt", $trace, true );

    if ( $GLOBALS ['padTraceTypes'] ['types'] )
      padFilePutContents ( "$location/types/$type.txt", $trace, true );

  }


  function padTraceFile ( $type, $extension, $data ) {  

    if ( ! $GLOBALS ['padTraceTypes'] ['files'] )
      return;

    global $padTrace, $padTraceDir, $padTraceActive;

    $padTraceActive = FALSE;

    padFilePutContents ( "$padTraceDir/files/$padTrace-$type.$extension", $data );
    
    $padTraceActive = TRUE;

  }


  function padTraceStatus ( ) {
  
    global $pad, $padNull, $padHit, $padElse, $padData, $padTraceChilds, $padTraceLevelDir;

    if     ( $padNull [$pad] ) $padTraceStatus = 'null';
    elseif ( $padHit  [$pad] ) $padTraceStatus = padTraceStatusGo ( 'hit'   );
    elseif ( $padElse [$pad] ) $padTraceStatus = padTraceStatusGo ( 'else'  );
    else                       $padTraceStatus = padTraceStatusGo ( 'other' );

    if ( ! padIsDefaultData ( $padData [$pad] ) and count ( $padData [$pad] ) ) 
      $padTraceStatus .= '-' . count ( $padData [$pad] ) ;

    touch ( $padTraceLevelDir [$pad] . "/status.$padTraceStatus.txt" );
  
    if ( $padTraceChilds [$pad] )
      rename ( $padTraceLevelDir [$pad], $padTraceLevelDir [$pad] . '-' . $padTraceChilds [$pad] );

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


  function padTraceCheckLocal ( ) {

    global $pad, $padTraceLevelDir;

    $file1 = $padTraceLevelDir [$pad] . '/trace.txt';
    $file2 = $padTraceLevelDir [$pad] . '/local.txt';

    if ( ! file_exists ( $file1 ) or ! file_exists ( $file2 ) )
      return;

    if ( filesize ( $file1 ) == filesize ( $file2 ) )
      unlink ( $padTraceLevelDir [$pad] . '/local.txt' );

  }


?>