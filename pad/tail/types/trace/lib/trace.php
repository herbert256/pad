<?php


  function padTrace ( $type, $event, $info='' ) {

    global $pad, $padOccur;
    global $padTraceMore, $padTraceRoot, $padTraceTree, $padTraceLocal, $padTraceSkipLevel;
    global $padTraceActive, $padTraceLine, $padTraceTypes, $padTraceId, $padTraceOccurId, $padTraceMaxLevel;

    if ( $padTraceSkipLevel and $padTraceSkipLevel == $pad ) return;
    if ( $padTraceMaxLevel  and $padTraceMaxLevel  >  $pad ) return;

    if ( padTraceSkip ( $type ) )
      return;
    
    $padTraceActive = FALSE;

    $padTraceLine++;

    padTail ( 'trace',  $type, $event, $info );

    $occur = $padOccur [$pad] ?? 0;

    if ( $event == 'start' )
      if     ( $type == 'level' ) $padTraceId [$pad]          = $padTraceLine;
      elseif ( $type == 'occur' ) $padTraceOccurId [$pad]     = $padTraceLine;
      else                        $GLOBALS ["padTraceX$type"] = $padTraceLine;

    padTraceInfo ( $trace, $info, $id, $type, $event );

    if ( $padTraceMore  ) padTraceMore  ( $trace, $info, $padTraceLine );
    if ( $padTraceRoot  ) padTraceRoot  ( $trace );
    if ( $padTraceTree  ) padTraceTree  ( $trace );
    if ( $padTraceLocal ) padTraceLocal ( $trace );
    if ( $padTraceTypes ) padTraceTypes ( $trace, $type );
   
    $padTraceActive = TRUE;

  }


  function padTraceSkip ( $type ) {

    global $pad;
    global $padTraceSkipLevel, $padTraceMaxLevel;

    if ( $padTraceSkipLevel and $padTraceSkipLevel == $pad )
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


  function padTraceRoot ( $trace ) {

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


  function padTraceTypes ( $trace, $type ) {  

    global $pad, $padTraceTypesDir;

    if ( $padTraceTypesDir )
      padTraceWrite ( $pad, "/types/$type.txt", $trace ); 
    else
      padTraceWrite ( $pad, "/types-$type.txt", $trace ); 

  }


  function padTraceWrite ( $pad, $location, $trace, $type='line' ) {  

    global $padOccur, $padTraceLevel,  $padTailDir, $padTraceLines, $padTraceSkipLevel, $padTraceMaxLevel ;

    if ( $padTraceSkipLevel and $padTraceSkipLevel == $pad ) return;
    if ( $padTraceMaxLevel  and $padTraceMaxLevel  >  $pad ) return;

    if ( ! isset ( $padTraceLevel [$pad] ) ) padTraceSet ( $pad );
    if ( ! $padTraceLevel [$pad]           ) padTraceSet ( $pad );

    if ( $pad < 0 or $padTraceLevel [$pad] == '*SKIP*' ) 
      $add = '';
    else
      $add = $padTraceLevel [$pad] . '/' . padTraceOccur ( $pad );

    $target = "$padTailDir/trace/$add$location";

    if ( ! $padTraceLines )
      return padTraceCheckDir ( padData . $target, $type );

    if ( $type == 'file' and padExists ( padData . $target ) )
      return;

    if ( $type == 'line' )
      padTailPut ( $target, $trace, TRUE );
    else
      padTailPut ( $target, $trace, FALSE );

  }

 
  function padTraceCheckDir ( $file, $type ) {

    if ( $type == 'file' )
      return;

    padTailChkDir ( $file );    

  }


  function padTraceOccur ( $pad ) {  

    global $padOccur;
    global $padTraceOccurs, $padTraceOccursSmart, $padTraceInitsExits, $padTraceDefault, $padTraceHideDefault;

    if ( $pad < 0 )
      return '';

    $occur = $padOccur [$pad] ?? 0;

    if     ( $padTraceInitsExits and $occur == 0     ) return 'inits/';
    elseif ( $padTraceInitsExits and $occur == 99999 ) return 'exits/';

    if ( ! $padTraceOccurs      ) return '';
    if ( ! $padTraceOccursSmart ) return "$occur/";

    if     ( $occur == 0              ) return '';
    elseif ( $occur == 99999          ) return '';
    elseif ( padTraceDefault ( $pad ) ) return '';
    else                                return "$occur/";

  }


?>