<?php


  function padTrace ( $type, $event, $info='' ) {

    if ( $GLOBALS ['padInTrace'] )
      return;

    $GLOBALS ['padInTrace'] = TRUE;

    global $pad, $padOccur, $padPage, $padReqID;
    global $padTrace, $padTraceTypes, $padTraceTree, $padTraceLocation;
 
    $padTrace++;

    if ( $event == 'start' ) {
      if     ( $type == 'level' ) $GLOBALS ['padTraceId']    [$pad] = $padTrace;
      elseif ( $type == 'occur' ) $GLOBALS ['padTraceOccur'] [$pad] = $padTrace;
      else                        $GLOBALS ["padTraceX$type"]       = $padTrace;
    }

    $prefix = ( $pad < 0 ) ? '0/0' : $pad . '/' . $padOccur [$pad];

    if     ( $type == 'level' )                      $id = $GLOBALS ['padTraceId']    [$pad] ?? 0;
    elseif ( $type == 'occur' )                      $id = $GLOBALS ['padTraceOccur'] [$pad] ?? 0;
    elseif ( isset ( $GLOBALS ["padTraceX$type"] ) ) $id = $GLOBALS ["padTraceX$type"]       ?? 0;
    else                                             $id = $padTrace;                                       

    $location = $padTraceLocation;

    $trace = sprintf ( '%-8s',  $prefix )
           . sprintf ( '%-8s',  $id     )
           . sprintf ( '%-10s', $type   )
           . sprintf ( '%-10s', $event  )
           . padMakeSafe ( $info, 80 );  

    if ( $padTraceTypes ['global'] ) 
      padTraceGlobal ( $location, $trace );

    if ( $padTraceTree ) 
      padTraceTree ( $location, $trace, $type );

    $GLOBALS ['padInTrace'] = FALSE;

  }


  function padTraceFile ( $type, $extension, $data ) {  

    padFilePutContents ( padTraceDir () . "/files/$type.$extension", $data );

  }


  function padTraceGlobal ( $location, $trace ) {  

    global $padTraceTypes, $padTraceTree;

    $file = ( $padTraceTypes ['nested'] ) ? 'global' : 'trace';

    if ( $padTraceTree )
      padTraceGo ( "$location/$file.txt", $trace );
    else
      padTraceGo ( "$location.txt", $trace );

  }


  function padTraceTree ( $location, $trace, $type ) {  

    global $pad, $padTraceTypes, $padTraceStart;

    $base = $location;

    if ( $padTraceTypes ['types-global'] )
      padTraceGo ( "$location/types/$type.txt", $trace );

    if ( $padTraceStart > $pad ) {

      padTraceNested ( $location, $padTraceTypes, $trace );

    } else {

      for ( $i = $padTraceStart; $i <= $pad; $i++ ) {

        $location .= padTraceDirOne ( $i );
        
        padTraceNested ( $location, $padTraceTypes, $trace );
      
      }

    }

    if ( $padTraceTypes ['types-local'] and $base <> $location )
      padTraceGo ( "$location/types/$type.txt", $trace );

    if ( $padTraceTypes ['local'] )
      if ( $padTraceTypes ['global'] or $padTraceTypes ['nested'] )
        padTraceGo ( "$location/local.txt", $trace );
      else
        padTraceGo ( "$location/trace.txt", $trace );

  }


 function padTraceNested ( $location, $types, $trace ) {  

    if ( $types ['nested'] )
      padTraceGo ( "$location/trace.txt", $trace );

  }


  function padTraceDir () {  

    global $pad, $padPage, $padReqID, $padTraceStart, $padTraceLocation;

    $dir = $padTraceLocation;

    for ( $i = $padTraceStart; $i <= $pad; $i++ )
      $dir .= padTraceDirOne ( $i );

    return $dir;

  }  


  function padTraceDirOne ( $i ) {  

    global $padOccur, $padTag, $padTraceId, $padTraceTypes, $padWalk, $padData;

    $dir = '';

    if ( $i > 0 )
      $dir .= '/' . $padTraceId [$i] . '-' . $padTag [$i];
    
    if ( $padOccur [$i] and $padTraceTypes ['occur'] )
      if ( $padWalk [$i] == 'next' or ! padIsDefaultData ($padData [$i] ) )
        $dir .= '/' . $padOccur [$i];
    
    return $dir;

  } 


  function padTraceGo ( $file, $trace ) {

    $file = str_replace ( '@', '_', $file );
    $file = str_replace ( "'", '_', $file );
    $file = str_replace ( '=', '_', $file );

    padFilePutContents ( $file, $trace, true );

  }
  

?>