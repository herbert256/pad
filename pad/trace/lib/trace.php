<?php


  function padTrace ( $type, $event, $info='' ) {

    global $pad, $padOccur;
    global $padTraceLine, $padTraceActive, $padTraceId, $padTraceOccurId;

    $padTraceActive = FALSE;

    $padTraceLine++;

    if ( $event == 'start' ) 
      padTraceStart ( $type, $padTraceLine );

    $prefix = $pad;  
    if ( $pad >= 0 and $padOccur [$pad] )
      $prefix .= '/' . $padOccur [$pad];

    $line = $padTraceLine;

    if     ( $type == 'level' )                      $id = $padTraceId [$pad]          ?? 0;
    elseif ( $type == 'occur' )                      $id = $padTraceOccurId [$pad]     ?? 0;
    elseif ( isset ( $GLOBALS ["padTraceX$type"] ) ) $id = $GLOBALS ["padTraceX$type"] ?? 0;
    else                                             $id = $padTraceLine;       

    if ( $id == $padTraceLine )
      $id = '';

    $trace = sprintf ( '%-7s',  $prefix )
           . sprintf ( '%-7s',  $line   )
           . sprintf ( '%-7s',  $id     )
           . sprintf ( '%-10s', $type   )
           . sprintf ( '%-10s', $event  )
           . padMakeSafe ( $info, 80 );  

    padTraceTreeGo ( $padTraceBase, $type, $trace );
    padTraceTree   ( $type, $trace )
    padTraceLocal  ( $trace );
   
    $padTraceActive = FALSE;

  }


  function padTraceStart ( $type, $line ) {  

    global $pad, $padOccur, $padWalk, $padTag, $padData;
    global $padTraceId, $padTraceOccurId, $padTraceLevelDir, $padTraceOccurDir;

    if     ( $type == 'level' ) $padTraceId [$pad]          = $line;
    elseif ( $type == 'occur' ) $padTraceOccurId [$pad]     = $line;
    else                        $GLOBALS ["padTraceX$type"] = $line;

    if ( $type == 'level' )
      if ( $pad == 0 )
        $padTraceLevelDir [$pad] = $padTraceBase .'/page' ;
      else
        $padTraceLevelDir [$pad] = $padTraceLevelDir [$pad-1] . "/$line-" . padFileCorrect ( $padTag [$i] );

    if ( $type == 'occur' ) {
      $occur = $padOccur [$pad];
      if ( $padWalk [$pad] <> 'next' and padIsDefaultData ( $padData [$pad] ) )
        $padTraceOccurDir [$pad] [$occur] = '';
      else
        $padTraceOccurDir [$pad] [$occur] = $padTraceLevelDir [$pad] . "/$occur";
    }

  }


  function padTraceTree ( $type, $trace ) {  

    global $pad, $padOccur;
    global $padTraceStart, $padTraceLevelDir, $padTraceOccurlDir;

    for ( $i = $padTraceStart; $i <= $pad; $i++ ) {

      padTraceTreeGo ( $padTraceLevelDir [$i], $type, $trace );

      $occur = $padOccur [$i];

      if ( $occur and $padTraceOccurlDir [$i] [$occur] ) 
        padTraceTreeGo ( $padTraceOccurlDir [$i] [$occur], $type, $trace );

    }

  }


  function padTraceTreeGo ( $location, $type, $trace ) {  

    global $padTraceTypes, $padTraceTypesDir;
 
    padFilePutContents ( "$location/trace.txt", $trace, true );

    if ( $padTraceTypes )
      if ( $padTraceTypesDir )
        padFilePutContents ( "$location/types/$type.txt", $trace, true );
      else
        padFilePutContents ( "$location/type-$type.txt", $trace, true );

  }


  function padTraceLocal ( $trace ) {  

    global $pad, $padOccur;
    global $padTraceBase, $padTraceLevelDir, $padTraceOccurlDir;

    if ( $pad < 0 )
      return padTraceLocal ( $padTraceBase, $trace );

    $occur = $padOccur [$pad];

    if ( ! $occur or ! $padTraceOccurlDir [$pad] [$occur] ) 
      padTraceLocal ( $padTraceLevelDir [$pad], $trace );
    else
      padTraceLocal ( $padTraceOccurlDir [$pad] [$occur], $trace );

  }


  function padTraceLocalGo ( $location, $trace ) {  

    padFilePutContents ( "$location/local.txt", $trace, true );

  }


  function padTraceFileLevel ( $level, $type, $extension, $data ) {

    global $padTraceLevelDir;

    padTraceFile ( $padTraceLevelDir [$level], $type, $extension, $data ) {  

  }  


  function padTraceFileOccur ( $level, $occur, $type, $extension, $data ) {

    global $padTraceLevelDir, $padTraceOccurDir;

    padTraceFile ( $xx, $type, $extension, $data ) {  

  }  


  function padTraceFile ( $location, $type, $extension, $data ) {  

    global $padTraceLine, $padTraceActive, $padTraceFiles, $padTraceFilesDir;

    if ( ! $padTraceFiles )
      return;

    $padTraceActive = FALSE;

    if ( $padTraceFilesDir )
      padFilePutContents ( "$location/files/$padTraceLine-$type.$extension", $data );
    else
      padFilePutContents ( "$location/$padTraceLine-$type.$extension", $data );
    
    $padTraceActive = TRUE;

  }


  function padTraceStatus ( ) {
  
    global $pad, $padNull, $padHit, $padElse, $padData, $padTraceChilds, $padTraceLevelDirName;

    if     ( $padNull [$pad] ) $padTraceStatus = 'null';
    elseif ( $padHit  [$pad] ) $padTraceStatus = padTraceStatusGo ( 'hit'   );
    elseif ( $padElse [$pad] ) $padTraceStatus = padTraceStatusGo ( 'else'  );
    else                       $padTraceStatus = padTraceStatusGo ( 'other' );

    if ( ! padIsDefaultData ( $padData [$pad] ) and count ( $padData [$pad] ) ) 
      $padTraceStatus .= '-' . count ( $padData [$pad] ) ;

    $base = padData . $padTraceLevelDirName [$pad];

    if ( file_exists ( $base ) ) {

      touch ( "$base/status.$padTraceStatus.txt" );
    
      if ( $padTraceChilds [$pad] )
        rename ( $base, $base . '-' . $padTraceChilds [$pad] );

    }

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