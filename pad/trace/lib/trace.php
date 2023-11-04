<?php


  function padTrace ( $type, $event, $info='' ) {

    global $pad, $padTraceActive, $padTraceLine, $padTraceBase, $padTraceSkipLevel, $padTraceMaxLevel;

    if ( $padTraceSkipLevel and $padTraceSkipLevel == $pad and $type == 'level' )
      return;

    if ( $padTraceMaxLevel and $padTraceMaxLevel > $pad )
      return;

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

    if     ( $type == 'trace' ) padTraceStartTrace ( $line );
    elseif ( $type == 'level' ) padTraceStartLevel ( $line );
    elseif ( $type == 'occur' ) padTraceStartOccur ( $line );
   
  }


 function padTraceStartTrace ( $line ) {  
    
    global $pad, $padTag, $padOccur;
    global $padTraceLevel, $padTraceOccur, $padTraceBase, $padTraceType;

    if ( $padTraceType == 'config') {

      $padTraceLevel [$pad]     = $padTraceBase;
      $padTraceOccur [$pad] [0] = $padTraceBase;
    
    } else {

      $last = $padOccur [$pad-1] ?? 0;

      $padTraceLevel [$pad]     = "$padTraceBase/$line-" . padFileCorrect ( $padTag [$pad] );
      $padTraceOccur [$pad] [0] = $padTraceLevel [$pad] . "/0";

      $padTraceLevel [$pad-1]         = $padTraceBase;
      $padTraceOccur [$pad-1] [$last] = $padTraceBase;

    }
    
  }


  function padTraceStartLevel ( $line ) {  

    global $pad, $padOccur, $padTag;
    global $padTraceLevel, $padTraceOccur, $padTraceBase, $padTraceStart, $padTraceType;

    if ( $padTraceType == 'option' and $pad == $padTraceStart )
      return; 

    $last = $padOccur [$pad-1] ?? 0;

    $padTraceLevel [$pad]  = $padTraceOccur [$pad-1] [$last];
    $padTraceLevel [$pad] .= "/$line-" . padFileCorrect ( $padTag [$pad] );

    $padTraceOccur [$pad] [0] = $padTraceLevel [$pad] . '/0';

  }

    
  function padTraceStartOccur ( $line ) {  
    
    global $pad, $padOccur, $padWalk, $padData;
    global $padTraceLevel, $padTraceOccur;

    $occur = $padOccur [$pad];

    $padTraceOccur [$pad] [$occur] = $padTraceLevel [$pad] . "/$occur";
    
  }


  function padTraceTrace ( &$trace, $type, $event ) {

    global $pad, $padOccur, $padTraces;
    global $padTraceLine, $padTraceId, $padTraceOccurId;

    $prefix = $pad;  
    if ( $pad >= 0 and $padOccur [$pad] )
      $prefix .= '/' . $padOccur [$pad];

    $line = $padTraceLine;

    if     ( $type == 'level' )                      $id = $padTraceId [$pad]          ?? 0;
    elseif ( $type == 'occur' )                      $id = $padTraceOccurId [$pad]     ?? 0;
    elseif ( isset ( $GLOBALS ["padTraceX$type"] ) ) $id = $GLOBALS ["padTraceX$type"] ?? 0;
    else                                             $id = $padTraceLine;       

    if ( ! $id or $id == $padTraceLine )
      $id = '';

    $trace = sprintf ( '%-4s',  $padTraces    )
           . sprintf ( '%-7s',  $prefix       )
           . sprintf ( '%-7s',  $padTraceLine )
           . sprintf ( '%-7s',  $id           )
           . sprintf ( '%-10s', $type         )
           . sprintf ( '%-10s', $event        );

  }


  function padTraceInfo ( &$trace, &$info ) {

    global $padTraceLine, $padTraceActive, $padTraceBase, $padTraceMore;

    if ( is_array ( $info ) )
      $info = padJson ( $info );

    $trace .= padMakeSafe ( $info, 60 );

    if ( $padTraceMore )
      if ( strlen ( $trace ) < 105 )
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