<?php


  function padTrace ( $type, $event, $info='' ) {

    if ( $GLOBALS ['padInTrace'] )
      return;

    $GLOBALS ['padInTrace'] = TRUE;

    global $pad, $padOccur, $padPage, $padReqID;
    global $padTrace, $padTraceId, $padTraceTypes, $padTraceTree;
 
    $padTrace++;

    if ( $type == 'level' and $event == 'start' ) 
      $padTraceId [$pad] = $padTrace;

    if ( $event == 'start' ) {
      if     ( $type == 'level' ) $GLOBALS ['padTraceLevel'] [$pad] = $padTrace;
      elseif ( $type == 'occur' ) $GLOBALS ['padTraceOccur'] [$pad] = $padTrace;
      else                        $GLOBALS ["padTrace$type"]        = $padTrace;
    }

    $prefix = ( $pad < 0 ) ? '0/0' : $pad . '/' . $padOccur [$pad];

    if     ( $type == 'level' )                     $id = $GLOBALS ['padTraceLevel'] [$pad] ?? 0;
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

    if ( $padTraceTree ) 
      padTraceTree ( $location, $trace, $type );

    $GLOBALS ['padInTrace'] = FALSE;

  }


  function padTraceFile ( $type, $extension, $data ) {  

    padFilePutContents ( padTraceDir () . "/$type.$extension", $data );

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

    global $pad, $padOccur, $padTag, $padData, $padWalk;
    global $padTraceId, $padTraceTypes, $padTraceStart;

    $local  = ( $padTraceTypes ['global'] or $padTraceTypes ['nested'] ) ? 'local' : 'trace';

    if ( $padTraceStart > $pad ) {

      if ( $padTraceTypes ['nested'] )
        padTraceGo ( "$location/trace.txt", $trace );

    } else {

      for ( $i = $padTraceStart; $i <= $pad; $i++ ) {

        if ( $i > 0 )
          $location .= '/' . $padTraceId [$i] . '-' . $padTag [$i];
        
        if ( $padOccur [$i] and $padTraceTypes ['occur'] )
          if ( $padWalk [$pad] == 'next' or ! padIsDefaultData ( $padData [$i] ) )
            $location .= '/' . $padOccur [$i];
        
        if ( $padTraceTypes ['nested'] )
          padTraceGo ( "$location/trace.txt", $trace );
      
      }

    }

    if ( $padTraceTypes ['local'] )
      padTraceGo ( "$location/$local.txt", $trace );

  }


  function padTraceDir () {  

    global $pad, $padOccur, $padTag, $padPage, $padReqID;
    global $padTraceId, $padTraceStart, $padTraceTypes, $padWalk, $padData;

    $dir = "trace/$padPage/$padReqID";

    for ( $i = $padTraceStart; $i <= $pad; $i++ ) {

      if ( $i > 0 )
        $dir .= '/' . $padTraceId [$i] . '-' . $padTag [$i];
      
      if ( $padOccur [$i] and $padTraceTypes ['occur'] )
        if ( $padWalk [$pad] == 'next' or ! padIsDefaultData ($padData [$i] ) )
          $dir .= '/' . $padOccur [$i];
    
    }

    return $dir;

  }  


  function padTraceGo ( $file, $trace ) {

    $file = str_replace ( '@', '_', $file );
    $file = str_replace ( "'", '_', $file );
    $file = str_replace ( '=', '_', $file );

    padFilePutContents ( $file, $trace, true );

  }
  

?>