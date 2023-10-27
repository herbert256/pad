<?php


  function padTrace ( $type, $event, $info='' ) {

    if ( $GLOBALS ['padInTrace'] )
      return;

    $GLOBALS ['padInTrace'] = TRUE;

    global $pad, $padOccur, $padPage, $padReqID;
    global $padTrace, $padTraceId, $padTraceTypes;
 
    $padTrace++;

    if ( $type == 'level' and $event == 'start' ) 
      $padTraceId [$pad] = $padTrace;

    if ( $event == 'start' ) {
      if     ( $type == 'level' ) $GLOBALS ['padTraceLevel'] [$pad] = $padTrace;
      elseif ( $type == 'occur' ) $GLOBALS ['padTraceOccur'] [$pad] = $padTrace;
      else                        $GLOBALS ["padTrace$type"]        = $padTrace;
    }

    $prefix = ( $pad < 0 ) ? '0/0' : $pad . '/' . $padOccur [$pad];

    if     ( $pad < 0         )                     $id = $padTrace;
    elseif ( $type == 'level' )                     $id = $GLOBALS ['padTraceLevel'] [$pad] ?? 0;
    elseif ( $type == 'occur' )                     $id = $GLOBALS ['padTraceOccur'] [$pad] ?? 0;
    elseif ( isset ( $GLOBALS ["padTrace$type"] ) ) $id = $GLOBALS ["padTrace$type"]        ?? 0;
    else                                            $id = $padTrace;                                       

    $location = "trace/$padPage/$padReqID";

    $trace = sprintf ( '%-8s',  $prefix )
           . sprintf ( '%-8s',  $id     )
           . sprintf ( '%-10s', $type   )
           . sprintf ( '%-10s', $event  )
           . padMakeSafe ( $info, 80 );  

    if ( $padTraceTypes ['global'] ) 
      padTraceGlobal ( $location, $trace );

    if ( $padTraceTypes ['tree'] ) 
      padTraceTree ( $location, $trace, $type );

    $GLOBALS ['padInTrace'] = FALSE;

  }


  function padTraceGlobal ( $location, $trace ) {  

    global $padTraceTypes;

    if ( $padTraceTypes ['tree'] )
      padTraceGo ( "$location/trace-global.txt", $trace );
    else
      padTraceGo ( "$location.txt", $trace );

  }


  function padTraceTree ( $location, $trace, $type ) {  

    global $pad, $padOccur, $padTag, $padData, $padWalk;
    global $padTraceId, $padTraceTypes, $padTraceStart;

    for ( $i = $padTraceStart; $i <= $pad; $i++ ) {

      if ( $i > 0 )
        $location .= '/' . $padTraceId [$i] . '-' . $padTag [$i];
      
      if ( $padOccur [$i] <> 0 and $padTraceTypes ['occur'] )
        if ( $padWalk [$pad] == 'next' or ! padIsDefaultData ($padData [$i] ) )
          $location .= '/' . $padOccur [$i];
      
      if ( $padTraceTypes ['nested'] )
        padTraceGo ( "$location/trace-nested.txt", $trace );
    
    }

    if ( $padTraceTypes ['types'] )
      padTraceGo ( "$location/$type.txt", $trace );

    if ( $padTraceTypes ['local'] )
      padTraceGo ( "$location/trace-local.txt", $trace );

    $GLOBALS ['padTraceDir'] = $location;

  }


  function padTraceGo ( $file, $trace ) {

    $file = str_replace ( '@', '_', $file );
    $file = str_replace ( "'", '_', $file );
    $file = str_replace ( '=', '_', $file );

    padFilePutContents ( $file, $trace, true );

  }
  

?>