<?php


  function padTrace ( $type, $event, $info='' ) {

    global $pad, $padOccur;
    global $padTraceMore, $padTraceTrace, $padTraceTree, $padTraceLocal;
    global $padTraceActive, $padTraceLine;

    if ( padTraceSkip ( $type ) )
      return;
    
    $padTraceActive = FALSE;

    if ( ! ( $event == 'start' and ( $type == 'trace' or $type == 'level' or $type == 'occur' ) ) )
      $padTraceLine++;

    padTraceInfo ( $trace, $info, $id, $type, $event );

    if ( $padTraceMore  ) padTraceMore  ( $trace, $info );
    if ( $padTraceTrace ) padTraceTrace ( $type, $trace );
    if ( $padTraceTree  ) padTraceTree  ( $type, $trace );
    if ( $padTraceLocal ) padTraceLocal ( $trace );
   
    $padTraceActive = TRUE;

  }


  function padTraceSkip ( $type ) {

    global $pad;
    global $padTraceMore, $padTraceTrace, $padTraceTree, $padTraceLocal;
    global $padTraceSkipLevel, $padTraceMaxLevel;

    if ( $padTraceSkipLevel and $padTraceSkipLevel == $pad and $type == 'level' )
      return TRUE;

    if ( $padTraceMaxLevel and $padTraceMaxLevel > $pad )
      return TRUE;

    if ( ! $padTraceMore and ! $padTraceTrace and ! $padTraceTree and ! $padTraceLocal )
      return TRUE;

    return FALSE;

  }


  function padTraceTrace ( $type, $trace ) {

    global $padTraceBase;

    padTraceLine  ( $type, $trace );

  }


  function padTraceMore ( $trace, $info ) {

    global $padTraceBase, $padTraceLine;

    $file = "$padTraceBase/more/$padTraceLine.txt";

    if ( str_ends_with ( $trace, ' <more>' ) and ! padExists ( padData . $file ) ) 
      padFilePutContents ( $file, $info );

  }


  function padTraceInfo ( &$trace, &$info, &$id, $type, $event ) {

    global $pad, $padOccur;
    global $padTraceLine, $padTraceId, $padTraceOccurId;

    $prefix = $pad;  
    if ( $pad >= 0 and $padOccur [$pad] )
      $prefix .= '/' . $padOccur [$pad];

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

    $info = padMakeSafe ( $info );

    if ( strlen ( $info ) > 70 ) 
      $trace .= substr ( $info, 0, 63 ) . ' <more>';
    else
      $trace .= $info;

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