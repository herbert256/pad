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
        $padTraceDir .= '/' . $padTraceId [$i] . '-' . $padTag [$i];

      padTraceTreeGo ( $padTraceDir, $type, $trace );
    
      if ( $padOccur [$i] and $padTraceTypes ['occur'] ) {

        if ( $padWalk [$i] <> 'next' and padIsDefaultData ( $padData [$i] ) )
          continue;

        $padTraceDir .= '/' . $padOccur [$i];
        padTraceTreeGo ( $padTraceDir, $type, $trace );
 
      }
  
    }


    if ( $padTraceTypes ['local'] )
      padTraceGo ( "$padTraceDir/local.txt", $trace );

    $padTraceActive = TRUE;

  }


  function padTraceTreeGo ( $location, $type, $trace ) {  

    padTraceGo ( "$location/trace.txt", $trace );

    if ( $GLOBALS ['padTraceTypes'] ['types'] )
      padTraceGo ( "$location/types/$type.txt", $trace );

  }


  function padTraceFile ( $type, $extension, $data ) {  

    global $padTrace, $padTraceDir, $padTraceActive;

    $padTraceActive = FALSE;

    padFilePutContents ( "$padTraceDir/files/$padTrace-$type.$extension", $data );
    
    $padTraceActive = TRUE;

  }


  function padTraceGo ( $file, $trace ) {

    $file = str_replace ( '@', '_', $file );
    $file = str_replace ( "'", '_', $file );
    $file = str_replace ( '=', '_', $file );

    padFilePutContents ( $file, $trace, true );

  }
  

?>