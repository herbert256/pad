<?php


  function padTrace ( $type, $event, $info='' ) {

    global $pad, $padOccur;
    global $padTrace, $padTraceDir, $padTraceActive, $padTraceLevel, $padTraceOccur;
    global $padTraceId, $padOccurId;

    $padTraceActive = FALSE;

    $padTrace++;
    $padTrace=80;

    if ( $event == 'start' ) {
      if     ( $type == 'level' ) $padTraceId [$pad]          = $padTrace;
      elseif ( $type == 'occur' ) $padTraceOccurId [$pad]     = $padTrace;
      else                        $GLOBALS ["padTraceX$type"] = $padTrace;
    }

    $prefix = $pad;  
    
    if ( isset ( $padOccur ) and isset ( $padOccur [$pad] ) and $padOccur [$pad] )
      $prefix .= '/' . $padOccur [$pad];

    if     ( $type == 'level' )                      $id = $padTraceId [$pad]          ?? 0;
    elseif ( $type == 'occur' )                      $id = $padTraceOccurId [$pad]     ?? 0;
    elseif ( isset ( $GLOBALS ["padTraceX$type"] ) ) $id = $GLOBALS ["padTraceX$type"] ?? 0;
    else                                             $id = $padTrace;       

    if ( $id == $padTrace )
      $id = '';

    $trace = sprintf ( '%-7s',  $prefix   )
           . sprintf ( '%-7s',  $padTrace )
           . sprintf ( '%-7s',  $id       )
           . sprintf ( '%-10s', $type     )
           . sprintf ( '%-10s', $event    )
           . padMakeSafe ( $info, 80 );  

    if ( $type == 'level' and $event == 'start' ) padTraceTree ( $type, $trace, 'set' );
    if ( $type == 'occur' and $event == 'start' ) padTraceTree ( $type, $trace, 'set' );

    if ( $type == 'level' and ! $padTraceLevel ) return padTraceActive ();
    if ( $type == 'occur' and ! $padTraceOccur ) return padTraceActive ();

    padTraceTree ( $type, $trace, 'trace' );

    padTraceActive ();

  }


  function padTraceActive () {

    $GLOBALS ['padTraceActive'] = TRUE;

  }


  function padTraceTree ( $type, $trace, $mode ) {

    global $pad, $padOccur, $padTag, $padWalk, $padData;
    global $padTrace, $padTraceId, $padTraceDirBase, $padTraceDir, $padTraceStart, $padTraceActive;
    global $padTraceLocal, $padTraceTrace, $padTraceLevelDir, $padTraceOccurDir;

    $padTraceDir = $padTraceDirBase;

    if ( $mode == 'trace' )
      padTraceTreeGo ( $padTraceDir, $type, $trace );

    for ( $i = $padTraceStart; $i <= $pad; $i++ ) {
 
      if ( $padTraceLevelDir ) {

        if ( $i == 0 )
          $padTraceDir .= '/page' ;
        else
          $padTraceDir .= '/' . $padTraceId [$i] . '-' .  padFileCorrect ( $padTag [$i] );

        if ( $mode == 'trace' )
          padTraceTreeGo ( $padTraceDir, $type, $trace );

      }
    
      if ( $padTraceOccurDir == 'never' )
 
        continue;
 
      elseif ( $padTraceOccurDir == 'always' ) 

        padTraceOccur ( $padOccur [$i], $type, $trace, $mode );
 
      elseif ( $padTraceOccurDir == 'smart' ) {
 
        if ( $padWalk [$i] <> 'next' and padIsDefaultData ( $padData [$i] ) )
          continue;
 
        if ( $padOccur [$i] )
          padTraceOccur ( $padOccur [$i], $type, $trace, $mode );
 
      }

    }

    if ( $mode == 'trace' and $padTraceLocal and ( $padTraceLevelDir or $padTraceOccurDir ) )
      if ( $padTraceTrace)
        padFilePutContents ( "$padTraceDir/local.txt", $trace, true );
      else
        padFilePutContents ( "$padTraceDir/trace.txt", $trace, true );

  }


  function padTraceOccur ( $occur, $type, $trace, $mode ) {  

    global $pad, $padTraceDir, $padTraceOccurDir;

    $padTraceOccurHasDir [$pad] [$occur] = TRUE;

    $padTraceDir .= '/' . $occur;
    
    if ( $mode == 'trace' )
      padTraceTreeGo ( $padTraceDir, $type, $trace );

  }


  function padTraceTreeGo ( $location, $type, $trace ) {  

    global $padTraceTrace, $padTraceTypes, $padTraceTypesDir;
 
    if ( $padTraceTrace )
      padFilePutContents ( "$location/trace.txt", $trace, true );

    if ( $padTraceTypes )
      if ( $padTraceTypesDir )
        padFilePutContents ( "$location/types/$type.txt", $trace, true );
      else
        padFilePutContents ( "$location/type-$type.txt", $trace, true );

  }


  function padTraceFile ( $type, $extension, $data ) {  

    global $padTrace, $padTraceDir, $padTraceActive, $padTraceFiles, $padTraceFilesDir;

    if ( ! $padTraceFiles )
      return;

    $padTraceActive = FALSE;

    if ( $padTraceFilesDir )
      padFilePutContents ( "$padTraceDir/files/$padTrace-$type.$extension", $data );
    else
      padFilePutContents ( "$padTraceDir/$padTrace-$type.$extension", $data );
    
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


  function padTraceCheckLocal ( $dir ) {

    global $pad;

    $file1 = pad . $dir . '/trace.txt';
    $file2 = pad . $dir . '/local.txt';

    if ( ! file_exists ( $file1 ) or ! file_exists ( $file2 ) )
      return;

    if ( filesize ( $file1 ) == filesize ( $file2 ) )
      unlink ( $file2 );

  }


?>