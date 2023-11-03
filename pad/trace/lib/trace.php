<?php


  function padTrace ( $type, $event, $info='' ) {

    global $padTraceActive, $padTraceLine, $padTraceBase;

    $padTraceActive = FALSE;

    $padTraceLine++;

    padTraceStart ( $type, $event, $padTraceLine );
    padTraceTrace ( $trace, $type, $event );
    padTraceInfo  ( $trace, $info );
    padTraceLine  ( $padTraceBase, $type, $trace );
    padTraceTree  ( $type, $trace );
    padTraceLocal ( $trace, $info, $type, $event );
   
    $padTraceActive = FALSE;

  }


  function padTraceStart ( $type, $event, $line ) {  

    if ( $event <> 'start' )
      return;
    
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


  function padTraceTrace ( &$trace, $type, $event ) {

    global $pad, $padOccur;
    global $padTraceLine, $padTraceId, $padTraceOccurId;

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
           . sprintf ( '%-10s', $event  );

  }


  function padTraceInfo ( &$trace, &$info ) {

    global $padTraceLine, $padTraceActive, $padTraceBase, $padTraceMore;

    if ( is_array ( $info ) )
      $info = padJson ( $info );

    $trace = padMakeSafe ( $trace . $info, 100 );

    if ( $padTraceMore )
      if ( strlen ( $trace ) < 100 )
        $info = '';
      else 
        $trace .= ' <more>';

  }


  function padTraceLine ( $location, $type, $trace ) {  

    global $padTraceTypes, $padTraceTypesDir;
 
    padFilePutContents ( "$location/trace.txt", $trace, true );

    if ( $padTraceTypes )
      if ( $padTraceTypesDir )
        padFilePutContents ( "$location/types/$type.txt", $trace, true );
      else
        padFilePutContents ( "$location/type-$type.txt", $trace, true );

  }


  function padTraceTree ( $type, $trace ) {  

    global $pad, $padOccur;
    global $padTraceStart, $padTraceLevelDir, $padTraceOccurlDir;

    for ( $i = $padTraceStart; $i <= $pad; $i++ ) {

      padTraceLine ( $padTraceLevelDir [$i], $type, $trace );

      $occur = $padOccur [$i];

      if ( $occur and $padTraceOccurlDir [$i] [$occur], $info ) 
        padTraceLine ( $padTraceOccurlDir [$i] [$occur], $type, $trace );

    }

  }


  function padTraceLocal ( $trace, $info, $type, $event ) {  

    global $pad, $padOccur;
    global $padTraceLine, $padTraceBase, $padTraceLevelDir, $padTraceOccurlDir;

    if ( $pad < 0 )
      return padTraceLocalGo ( $padTraceBase, $trace, $info );

    $occur = $padOccur [$pad];

    if ( ! $occur or ! $padTraceOccurlDir [$pad] [$occur] ) 
      padTraceLocalGo ( $padTraceLevelDir [$pad], $trace, $info );
    else
      padTraceLocalGo ( $padTraceOccurlDir [$pad] [$occur], $trace, $info );

  }


  function padTraceLocalGo ( $location, $trace, $info ) {  

    global $padTraceMore, $padTraceLine;

    padFilePutContents ( "$location/local.txt", $trace, true );

    if ( $info and $padTraceMore )
      padFilePutContents ( "$location/more/$padTraceLine-$type-$event.txt", $info );

  }


?>