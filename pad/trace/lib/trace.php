<?php


  function padTrace ( $type, $event, $info='' ) {

    global $pad, $padOccur;
    global $padTraceMore, $padTraceTrace, $padTraceTree, $padTraceLocal, $padTraceXml;
    global $padTraceActive, $padTraceLine;

    if ( padTraceSkip ( $type ) )
      return;
    
    $padTraceActive = FALSE;

    if ( ! ( $event == 'start' and ( $type == 'trace' or $type == 'level' or $type == 'occur' ) ) )
      $padTraceLine++;

    padTraceInfo ( $trace, $info, $short, $id, $type, $event );

    if ( $padTraceMore  ) padTraceMore  ( $info, $type, $event );
    if ( $padTraceTrace ) padTraceTrace ( $type, $trace );
    if ( $padTraceTree  ) padTraceTree  ( $type, $trace );
    if ( $padTraceLocal ) padTraceLocal ( $trace );
    if ( $padTraceXml   ) padTraceXml   ( $short, $id, $type, $event );
   
    $padTraceActive = TRUE;

  }


  function padTraceSkip ( $type ) {

    global $pad;
    global $padTraceMore, $padTraceTrace, $padTraceTree, $padTraceLocal, $padTraceXml;
    global $padTraceSkipLevel, $padTraceMaxLevel;

    if ( $padTraceSkipLevel and $padTraceSkipLevel == $pad and $type == 'level' )
      return TRUE;

    if ( $padTraceMaxLevel and $padTraceMaxLevel > $pad )
      return TRUE;

    if ( ! $padTraceMore and ! $padTraceTrace and ! $padTraceTree and ! $padTraceLocal and ! $padTraceXml )
      return TRUE;

    return FALSE;

  }


  function padTraceTrace ( $type, $trace ) {

    global $padTraceBase;

    padTraceLine  ( $type, $trace );

  }


  function padTraceMore ( $info, $type, $event ) {

    if ( ! $info )
      return;

    global $padTraceBase, $padTraceLine;

    padFilePutContents ( "$padTraceBase/more/$padTraceLine-$type-$event.txt", $info );

  }


  function padTraceInfo ( &$trace, &$info, &$short, &$id, $type, $event ) {

    global $pad, $padOccur;
    global $padTraceLine, $padTraceId, $padTraceMore, $padTraceOccurId, $padTraceXml;

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

    $trace = sprintf ( '%-7s',  $prefix       )
           . sprintf ( '%-7s',  $padTraceLine )
           . sprintf ( '%-7s',  $id           )
           . sprintf ( '%-10s', $type         )
           . sprintf ( '%-10s', $event        );

    if ( is_array ( $info ) )
      $info = padJson ( $info );

    $save = padMakeSafe ( $info );

    if ( strlen ( $save ) > 70 and $padTraceMore ) {
      $short  = substr ( $save, 0, 63 ) . ' <more>';
      $trace .= $short;
    } else {
      $short  = $save;
      $trace .= $save;
      $info   = '';
    }

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
    global $padTraceGo, $padTraceLevel, $padTraceOccur;

    for ( $i = $padTraceGo; $i <= $pad; $i++ ) {

      padTraceLine ( $padTraceLevel [$i], $type, $trace );

      $occur = $padOccur [$i];

      if ( $occur and $padTraceOccur [$i] [$occur] ) 
        padTraceLine ( $padTraceOccur [$i] [$occur], $type, $trace );

    }

  }


  function padTraceLocal ( $trace ) {  

    global $pad, $padOccur;
    global $padTraceLine, $padTraceBase, $padTraceLevel, $padTraceOccur;

    if ( $pad < 0 )
      return padTraceLocalGo ( $padTraceBase, $trace );

    $occur = $padOccur [$pad];

    if ( ! $occur or ! $padTraceOccur [$pad] [$occur] ) 
      padTraceLocalGo ( $padTraceLevel [$pad], $trace );
    else
      padTraceLocalGo ( $padTraceOccur [$pad] [$occur], $trace );

  }


  function padTraceLocalGo ( $location, $trace ) {  

    padFilePutContents ( "$location/local.txt", $trace, true );

  }


?>