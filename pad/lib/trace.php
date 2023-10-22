<?php


  function padTrace ( $type, $event, $info ) {

    if ( ! isset ( $GLOBALS ['padTraceTypes'] [$type] ) )
      return;

    global $pad, $padTrace, $padOccur, $padPage, $padReqID;
 
    $padTrace++;

    if ( $event == 'start' ) {
      if     ( $type == 'level' ) $GLOBALS ['padTraceLevel'] [$pad] = $padTrace;
      elseif ( $type == 'occur' ) $GLOBALS ['padTraceOccur'] [$pad] = $padTrace;
      else                        $GLOBALS ["padTrace$type"]        = $padTrace;
    }

    if     ( $type == 'level' )                     $id = $GLOBALS ['padTraceLevel'] [$pad];
    elseif ( $type == 'occur' )                     $id = $GLOBALS ['padTraceOccur'] [$pad];
    elseif ( isset ( $GLOBALS ["padTrace$type"] ) ) $id = $GLOBALS ["padTrace$type"];
    else                                            $id = $padTrace;                                       

    padFilePutContents ( 

      "trace/$padPage/$padReqID.txt",

        sprintf ( '%-8s',  $pad . '/' . $padOccur [$pad] )
      . sprintf ( '%-8s',  $id    )
      . sprintf ( '%-10s', $type  )
      . sprintf ( '%-10s', $event )
      . padMakeSafe ( $info, 80 ),  

      true

    );

  }

  
?>