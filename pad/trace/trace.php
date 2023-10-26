<?php


  function padTraceGo ( $file, $trace ) {

    $file = str_replace ( '@', '_', $file );
    $file = str_replace ( "'", '_', $file );
    $file = str_replace ( '=', '_', $file );

    padFilePutContents ( $file, $trace, true );

  }


  function padTrace ( $type, $event, $info='' ) {

    global $padTrace, $padTraceId, $padTraceTypes;
    global $pad, $padOccur, $padTag, $padPage, $padReqID ;
 
    $padTrace++;

    if ( $type == 'level' and $event == 'start' ) 
      $padTraceId [$pad] = $padTrace;

    if ( $event == 'start' ) {
      if     ( $type == 'level' ) $GLOBALS ['padTraceLevel'] [$pad] = $padTrace;
      elseif ( $type == 'occur' ) $GLOBALS ['padTraceOccur'] [$pad] = $padTrace;
      else                        $GLOBALS ["padTrace$type"]        = $padTrace;
    }

    $prefix = ( $pad < 0 ) ? '0/0' : '/' . $padOccur [$pad];

    if     ( $pad < 0         )                     $id = $padTrace;
    elseif ( $type == 'level' )                     $id = $GLOBALS ['padTraceLevel'] [$pad] ?? 0;
    elseif ( $type == 'occur' )                     $id = $GLOBALS ['padTraceOccur'] [$pad] ?? 0;
    elseif ( isset ( $GLOBALS ["padTrace$type"] ) ) $id = $GLOBALS ["padTrace$type"]        ?? 0;
    else                                            $id = $padTrace;                                       

    $trace = sprintf ( '%-8s',  $prefix )
           . sprintf ( '%-8s',  $id     )
           . sprintf ( '%-10s', $type   )
           . sprintf ( '%-10s', $event  )
           . padMakeSafe ( $info, 80 );  

    $location = "trace/$padPage/$padReqID";

    if ( $padTraceTypes ['global'] )
      if ( $padTraceTypes ['tree'] )
        padTraceGo ( "$location/global.txt", $trace );
      else
        padTraceGo ( "$location.txt", $trace );

    if ( ! $padTraceTypes ['tree'] )
      return;

    for ( $i = 0; $i <= $pad; $i++ ) {
      if ( $i > 0 )
        $location .= '/' . $padTraceId [$i] . '-' . $padTag [$i];
      if ( $padOccur [$i] <> 0 and $padTraceTypes ['occur'] )
        $location .= '/' . $padOccur [$i];
      if ( $padTraceTypes ['nested'] )
        padTraceGo ( "$location/trace.txt", $trace );
    }

    if ( $padTraceTypes ['types'] )
      padTraceGo ( "$location/$type.txt", $trace );

    if ( $padTraceTypes ['local'] )
      padTraceGo ( "$location/local.txt", $trace );

    if ( $type == 'error' ) {
      $id = uniqid ();
      padDumpToDir ( $info, "$location/ERROR-$id" );
    }

    $GLOBALS ['padTraceDir'] = $location;

  }
  
?>