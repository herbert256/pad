<?php


  function padTrace ( $type, $event, $info='' ) {

    global $pad, $padOccur;
    global $padTraceMore, $padTraceTrace, $padTraceTree, $padTraceLocal;
    global $padTraceActive, $padTraceLine, $padTraceTypes, $padTraceId, $padTraceOccurId;

    if ( padTraceSkip ( $type ) )
      return;
    
    $padTraceActive = FALSE;

    $padTraceLine++;

    $occur = $padOccur [$pad] ?? 0;

    if ( $event == 'start' )
      if     ( $type == 'level' ) $padTraceId [$pad]          = $padTraceLine;
      elseif ( $type == 'occur' ) $padTraceOccurId [$pad]     = $padTraceLine;
      else                        $GLOBALS ["padTraceX$type"] = $padTraceLine;

    padTraceInfo ( $trace, $info, $id, $type, $event );

    if ( $padTraceMore  ) padTraceMore  ( $trace, $info, $padTraceLine );
    if ( $padTraceTrace ) padTraceTrace ( $trace );
    if ( $padTraceTree  ) padTraceTree  ( $trace );
    if ( $padTraceLocal ) padTraceLocal ( $trace );
    if ( $padTraceTypes ) padTraceType  ( $trace, $type );
   
    $padTraceActive = TRUE;

  }


  function padTraceSkip ( $type ) {

    global $pad;
    global $padTraceSkipLevel, $padTraceMaxLevel;

    if ( $padTraceSkipLevel and $padTraceSkipLevel == $pad and $type == 'level' )
      return TRUE;

    if ( $padTraceMaxLevel and $padTraceMaxLevel > $pad )
      return TRUE;

    return FALSE;

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

    $trace = sprintf ( '%-9s',  $prefix       )
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


  function padTraceMore ( $trace, $info, $line ) {

    if ( str_ends_with ( $trace, ' <more>' )  )
      padTraceWrite ( -1, "more/$line.txt", $info, 'file' ) ;

  }


  function padTraceTrace ( $trace ) {

    padTraceWrite ( -1, 'trace.txt', $trace );

  }


  function padTraceTree ( $trace ) {  

    global $pad;
    global $padTraceGo;

    for ( $i = $padTraceGo; $i <= $pad; $i++ )
      padTraceWrite ( $i, 'trace.txt', $trace );

  }

  function padTraceLocal ( $trace ) {  

    global $pad;

    padTraceWrite ( $pad, 'local.txt', $trace );

  }


  function padTraceType ( $trace, $type ) {  

    global $pad, $padTraceTypesDir;

    if ( $padTraceTypesDir )
      padTraceWrite ( $pad, "/types/$type.txt", $trace ); 
    else
      padTraceWrite ( $pad, "/types-$type.txt", $trace ); 

  }


  function padTraceWrite ( $pad, $location, $trace, $type='line' ) {  

    global $padOccur, $padTraceLevel,  $padTraceBase, $padTraceOnlyDirs ;

    if ( ! isset ( $padTraceLevel [$pad] ) ) padTraceSet ( $pad );
    if ( ! $padTraceLevel [$pad]           ) padTraceSet ( $pad );

    $add = ( $pad < 0 ) ? '' : $padTraceLevel [$pad] . '/' . padTraceOccur ( $pad );

    $target = "$padTraceBase/$add$location";

    if ( $padTraceOnlyDirs )
      return padTraceOnlyDirs ( padData . $target, $type );

    if ( $type == 'file' and padExists ( padData . $target ) )
      return;

    if ( $type == 'line' )
      padFilePutContents ( $target, $trace, TRUE );
    else
      padFilePutContents ( $target, $trace, FALSE );

  }


  function padTraceOnlyDirs ( $file, $type ) {

    if ( $type == 'file' )
      return;

    padFileChkDir ( $file );    

  }


  function padTraceOccur ( $pad ) {  

    global $padOccur;
    global $padTraceOccurs, $padTraceInitsExits, $padTraceDefault, $padTraceHideDefault;

    if ( $pad < 0 or $padTraceOccurs == 'never' )
      return '';

    $occur = $padOccur [$pad] ?? 0;

    if ( $padTraceOccurs == 'always' ) 
      if     ( $occur == 0     ) return 'inits/';
      elseif ( $occur == 99999 ) return 'exits/';
      else                       return "$occur/";

    if (   $padTraceInitsExits and $occur == 0     ) return 'inits/';
    elseif (   $padTraceInitsExits and $occur == 99999 ) return 'exits/';
    elseif ( ! $padTraceInitsExits and $occur == 0     ) return '';
    elseif ( ! $padTraceInitsExits and $occur == 99999 ) return '';

    if ( $padTraceHideDefault and $occur == 1 and padTraceDefault ( $pad ) ) 
      return '';

    return "$occur/";

  }


?>