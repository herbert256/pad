<?php


  function padTraceSet ( $type, $event ) {  

    if ( $event == 'start' ) padTraceSetStart ( $type );
    else                     padTraceSetEnd   ( $type );

  }


  function padTraceSetStart ( $type ) {  

    global $pad;
    global $padTraceId, $padTraceOccurId, $padTraceLine;

    $padTraceLine++;

    if     ( $type == 'level' ) $padTraceId [$pad]          = $padTraceLine;
    elseif ( $type == 'occur' ) $padTraceOccurId [$pad]     = $padTraceLine;
    else                        $GLOBALS ["padTraceX$type"] = $padTraceLine;

    if     ( $type == 'trace' ) padTraceSetTrace ( $padTraceLine );
    elseif ( $type == 'level' ) padTraceSetLevel ( $padTraceLine );
    elseif ( $type == 'occur' ) padTraceSetOccur ( $padTraceLine );
   

  }


  function padTraceSetEnd ( $type ) {  

   
  }


  function padTraceSetTrace ( $line ) {  
    
    global $pad, $padTag, $padOccur;
    global $padTraceLevel, $padTraceOccur, $padTraceBase, $padTraceType, $padTraceAddOccur;

    if ( $padTraceType == 'config') {

      $padTraceLevel [$pad]     = $padTraceBase;
      $padTraceOccur [$pad] [0] = $padTraceBase;
    
    } else {

      $last = $padOccur [$pad-1] ?? 0;

      $padTraceLevel [$pad]     = $padTraceBase         . padTraceSetAdd ( $line );
      $padTraceOccur [$pad] [0] = $padTraceLevel [$pad] . padTraceSetAddOccur ( 0 );

    }
    
    padTraceSetGo ( $padTraceLevel [$pad] );

  }


  function padTraceSetLevel ( $line ) {  

    global $pad, $padOccur;
    global $padTraceLevel, $padTraceOccur, $padTraceBase, $padTraceGo, $padTraceType, $padTraceAddOccur;

    if ( $padTraceType == 'option' and $pad == $padTraceGo )
      return; 

    $last = $padOccur [$pad-1] ?? 0;

    $padTraceLevel [$pad]     = $padTraceOccur 
    [$pad-1] 
    [$last] . padTraceSetAdd ( $line );
    $padTraceOccur [$pad] [0] = $padTraceLevel [$pad]           . padTraceSetAddOccur ( 0 );

    padTraceSetGo ( $padTraceLevel [$pad] );

  }

    
  function padTraceSetOccur ( $line ) {  
    
    global $pad, $padOccur, $padWalk, $padData;
    global $padTraceLevel, $padTraceOccur;

    $occur = $padOccur [$pad];

    $padTraceOccur [$pad] [$occur] = $padTraceLevel [$pad] . padTraceSetAddOccur ( $occur );

    if ( padTraceSetAddOccur ( $occur ) )
      padTraceSetGo ( $padTraceOccur [$pad] [$occur] );
    
  }


  function padTraceSetGo ( $dir ) {  

    global $padTraceMkDir, $padTraceStructure, $padTraceBase, $padTraceActive;

    if ( $padTraceMkDir and ! file_exists ( padData . $dir ))
      mkdir ( padData . $dir, 0777, TRUE );

    if ( ! $padTraceStructure )
      return;

    $info = str_replace( $padTraceBase, '', $dir );

    $padTraceActive = FALSE;
    padFilePutContents ( "$padTraceBase/trace.txt", $info, true );
    $padTraceActive = TRUE;
 
  }
 

  function padTraceSetAdd ( $line ) {  

    global $pad, $padTag;
    global $padTraceAddLine;

    if ( $padTraceAddLine )
      return "/$line-" . padFileCorrect ( $padTag [$pad] );
    else 
      return '/' . padFileCorrect ( $padTag [$pad] );

  }  


  function padTraceSetAddOccur ( $occur ) {  

    global $pad, $padWalk, $padData;
    global $padTraceAddOccur, $padTraceOccurSmart;

    if ( ! $padTraceAddOccur )
      return '';
    
    if ( ! $padTraceOccurSmart )
      return "/$occur";
    
    if ( $padWalk [$pad] <> 'next' and padIsDefaultData ( $padData [$pad] ) )
      return '';

    return "/$occur";
    
  }


?>