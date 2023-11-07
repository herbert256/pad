<?php


  function padTraceXml ( $trace, $info, $id, $type, $event ) {

    global $pad, $padTraceXmlLines;

    if ( ( $type == 'trace' or $type == 'level' or $type == 'occur' ) and 
         ( $event == 'start' or $event == 'end' ) )
      return;

    if ( $event == 'start' )
      padTraceXmlWrite ( "<$type>", 'start' );

    if ( $padTraceXmlLines )
      padTraceXmlLine ( $trace, $info, $type, $event, $id );

    if ( $event == 'end' )
      padTraceXmlWrite ( "</$type>", 'end' );

  }


  function padTraceXmlSet ( $type, $event ) {

    if     ( $type == 'trace' and $event == 'start' ) padTraceXmlTraceStart ();
    elseif ( $type == 'trace' and $event == 'end'   ) padTraceXmlTraceEnd   ();
    elseif ( $type == 'level' and $event == 'start' ) padTraceXmlLevelStart ();
    elseif ( $type == 'level' and $event == 'end'   ) padTraceXmlLevelEnd   ();
    elseif ( $type == 'occur' and $event == 'start' ) padTraceXmlOccurStart ();
    elseif ( $type == 'occur' and $event == 'end'   ) padTraceXmlOccurEnd   ();

  }


  function padTraceXmlTraceStart () {

    padTraceXmlWrite ( '<trace>', 'start' );

  }


  function padTraceXmlTraceEnd () {

    padTraceXmlWrite ( '</trace>', 'end' );

  }


  function padTraceXmlLevelStart () {

    global $pad, $padTag;

    $tag     = $padTag [$pad];
    $type    = $GLOBALS ['padTypeResult'] ?? '';
    $pair    = $GLOBALS ['padPairSet']    ?? '';
    $prmType = $GLOBALS ['padPrmTypeSet'] ?? '';

    $info = "<$tag" 
          . " level=\"$pad\""
          . " type=\"$type\""
          . " pair=\"$pair\""
          . " parms=\"$prmType\""
          . ' >';

    padTraceXmlWrite ( $info, 'start' );
  
  }


  function padTraceXmlLevelEnd () {

    global $pad, $padTag;

    $tag = $padTag [$pad];
  
    padTraceXmlWrite ( "</$tag>", 'end' );

  }


  function padTraceXmlOccurStart () {

    global $pad, $padOccur, $padOccurType;

    $occur = $padOccur [$pad];
    $type  = $padOccurType [$pad];

    padTraceXmlWrite ( "<occur nr=\"$occur\" type=\"$type\" >", 'start' );

  }


  function padTraceXmlOccurEnd () {

    padTraceXmlWrite ( "</occur>", 'end' );

  }


  function padTraceXmlLine ( $trace, $info, $type, $event, $id ) {

    global $padTraceXmlNoEmpty;

    if ( ! $info and $padTraceXmlNoEmpty )
      return;

    if ( $event == 'start' or $event == 'end' ) {

      if ( $info )
        padTraceXmlLineInParent ( $info, $event );

    } else {

      if ( $id )
        padTraceXmlLineInParent ( $info, $event );
      else
        padTraceXmlIndependedLine ( $info, $type, $event );

    }

  }


  function padTraceXmlIndependedLine ( $info, $type, $event ) {

    padTraceXmlLineWrite ( "$type event=\"$event\"", $info );

  }


  function padTraceXmlLineInParent ( $info, $event ) {

    padTraceXmlLineWrite ( $event, $info );

  }


  function padTraceXmlLineWrite ( $xml, $info ) {

    global $padTraceLine, $padTraceBase;

    $extra = ' ';
 
    if ( strlen ( $info ) > 30 and padTraceXmlIsHtml ( $info ) ) {

      $extra = " more=\"$padTraceLine\" ";

      $file = "$padTraceBase/more/$padTraceLine.txt";

      if ( ! padExists ( padData . $file ) ) 
        padFilePutContents ( $file, $info );

      $info = substr ( $info, 0, 30 );
   
    } elseif ( strlen ( $info ) > 50  ) {

      $extra = " more=\"$padTraceLine\" ";

      $file = "$padTraceBase/more/$padTraceLine.txt";

      if ( ! padExists ( padData . $file ) ) 
        padFilePutContents ( $file, $info );

      $info = substr ( $info, 0, 50 );
   
    } 

    $info = htmlspecialchars ( $info );
    
    padTraceXmlWrite ( "<$xml value=\"$info\"$extra/>" );

  }


  function padTraceXmlIsHtml ( $data ) {

    return  ( strpos( $data , '<') !== FALSE and strpos( $data , '>' ) !== FALSE  ) ;

  }


  function padTraceXmlWrite ( $info, $action='', $initsExits=TRUE ) {

    global $pad, $padTraceBase, $padTraceSpaces, $padTraceActive, $padTraceXmlWhere;

    $where = $padTraceXmlWhere [$pad];

    if ( $initsExits )
      padTraceXmlInitsExits ();

    if ( $action == 'end' ) 
      $padTraceSpaces = $padTraceSpaces - 2;

    if ( $padTraceSpaces >= 0 )
      $spaces = str_repeat ( ' ', $padTraceSpaces );
    else
      $spaces = '???';
    
    $padTraceActive = FALSE;
    padFilePutContents ( $padTraceBase . '/trace.xml', $spaces . "$info", true );
    $padTraceActive = TRUE;

    if ( $action == 'start' ) 
      $padTraceSpaces = $padTraceSpaces + 2;
  
  }


   function padTraceXmlInitsExits () {

    global $pad;
    global $padTraceXmlInitsExits, $padTraceXmlWhere, $padTraceXmlExitsOpened;
    global $padTraceXmlInitsOpened, $padTraceXmlOccurOpened, $padTraceXmlExitsOpened;

    if ( ! $padTraceXmlInitsExits )
      return;

    if ( $padTraceXmlWhere [$pad] == 'inits' and ! $padTraceXmlInitsOpened [$pad] ) {

      $padTraceXmlInitsOpened [$pad] = TRUE;
      padTraceXmlWrite ( '<inits>', 'start', FALSE );

    } elseif ( $padTraceXmlWhere [$pad] == 'occurs' and ! $padTraceXmlOccurOpened [$pad] ) {

      padTraceXmlInitsOpened ();

      $padTraceXmlOccurOpened [$pad] = TRUE;
      if ( $padTraceXmlExitsOpened )
        padTraceXmlWrite ( '<occurs>', 'start', FALSE );

    } elseif ( $padTraceXmlWhere [$pad] == 'exits' and ! $padTraceXmlExitsOpened [$pad] ) {

      padTraceXmlInitsOpened ();
      padTraceXmlOccurOpened ();

      $padTraceXmlExitsOpened [$pad] = TRUE;
      padTraceXmlWrite ( '<exits>', 'start', FALSE );

    }

  }


  function padTraceXmlInitsOpened () {

    global $pad;
    global $padTraceXmlInitsOpened, $padTraceXmlInitsClosed;

    if ( $padTraceXmlInitsOpened [$pad] and ! $padTraceXmlInitsClosed [$pad] ) {
      $padTraceXmlInitsClosed [$pad] = TRUE;
      padTraceXmlWrite ( '</inits>', 'end', FALSE );
    }

  }


  function padTraceXmlOccurOpened () {

    global $pad;
    global $padTraceXmlOccurOpened, $padTraceXmlOccurClosed, $padTraceXmlExitsOpened;

    if ( $padTraceXmlOccurOpened [$pad] and ! $padTraceXmlOccurClosed [$pad] ) {

      $padTraceXmlOccurClosed [$pad] = TRUE;
      
      if ( $padTraceXmlExitsOpened )
        padTraceXmlWrite ( '</occurs>', 'end', FALSE );
    
    }

  }


  function padTraceXmlExitsOpened () {

    global $pad;
    global $padTraceXmlExitsOpened, $padTraceXmlExitsClosed;

    if ( $padTraceXmlExitsOpened [$pad] and ! $padTraceXmlExitsClosed [$pad] ) {
      $padTraceXmlExitsClosed [$pad] = TRUE;
      padTraceXmlWrite ( '</exits>', 'end', FALSE );
    }

  }


  function padTraceShowOccurs () {
  
    global $pad, $padWalk, $padData, $padBase;
    global $padTraceShowOccurs;

    if ( $padWalk [$pad] == 'next'                 or 
         count ( $padData [$pad] ) > 1             or 
         padOpenCloseOk ( $padBase[$pad], '@end@') or 
         padOpenCloseOk ( $padBase[$pad], '@start@')  )

      $padTraceShowOccurs [$pad] = TRUE;

  }


?>