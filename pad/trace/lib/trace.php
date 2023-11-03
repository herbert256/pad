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
   
    $padTraceActive = TRUE;

  }


  function padTraceStart ( $type, $event, $line ) {  

    if ( $event <> 'start' )
      return;

    global $pad, $padTraceId, $padTraceOccurId;

    if     ( $type == 'level' ) $padTraceId [$pad]          = $line;
    elseif ( $type == 'occur' ) $padTraceOccurId [$pad]     = $line;
    else                        $GLOBALS ["padTraceX$type"] = $line;

    if     ( $type == 'level' ) padTraceStartLevel ( $line );
    elseif ( $type == 'occur' ) padTraceStartOccur ( $line );
   
  }


  function padTraceStartLevel ( $line ) {  

    global $pad, $padOccur, $padTag;
    global $padTraceLevel, $padTraceOccur, $padTraceBase;

    if ( $pad == 0 )

      $padTraceLevel [$pad] = $padTraceBase .'/page' ;

    else {

      $occur = $padOccur [$pad-1];
      $add   = "/$line-" . padFileCorrect ( $padTag [$pad] );

      if ( $padTraceOccur [$pad-1] [$occur] )
        $padTraceLevel [$pad] = $padTraceOccur [$pad-1] [$occur] . $add;
      else
        $padTraceLevel [$pad] = $padTraceLevel [$pad-1]          . $add;

    }

  }

    
  function padTraceStartOccur ( $line ) {  
    
    global $pad, $padOccur, $padWalk, $padData;
    global $padTraceLevel, $padTraceOccur;

    $occur = $padOccur [$pad];

    $padTraceOccur [$pad] [$occur] = '';

    if ( $padWalk [$pad] <> 'next' and padIsDefaultData ( $padData [$pad] ) )
      return;

    $padTraceOccur [$pad] [$occur] = $padTraceLevel [$pad] . "/$occur";
    
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

    $trace .= padMakeSafe ( $info, 80 );

    if ( $padTraceMore )
      if ( strlen ( $trace ) < 120 )
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
    global $padTraceStart, $padTraceLevel, $padTraceOccur;

    for ( $i = $padTraceStart; $i <= $pad; $i++ ) {

      padTraceLine ( $padTraceLevel [$i], $type, $trace );

      $occur = $padOccur [$i];

      if ( $occur and $padTraceOccur [$i] [$occur] ) 
        padTraceLine ( $padTraceOccur [$i] [$occur], $type, $trace );

    }

  }


  function padTraceLocal ( $trace, $info, $type, $event ) {  

    global $pad, $padOccur;
    global $padTraceLine, $padTraceBase, $padTraceLevel, $padTraceOccur;

    if ( $pad < 0 )
      return padTraceLocalGo ( $padTraceBase, $trace, $info, $type, $event );

    $occur = $padOccur [$pad];

    if ( ! $occur or ! $padTraceOccur [$pad] [$occur] ) 
      padTraceLocalGo ( $padTraceLevel [$pad], $trace, $info, $type, $event );
    else
      padTraceLocalGo ( $padTraceOccur [$pad] [$occur], $trace, $info, $type, $event );

  }


  function padTraceLocalGo ( $location, $trace, $info, $type, $event ) {  

    global $padTraceMore, $padTraceLine;

    padFilePutContents ( "$location/local.txt", $trace, true );

    if ( $info and $padTraceMore )
      padFilePutContents ( "$location/more/$padTraceLine-$type-$event.txt", $info );

  }


?>