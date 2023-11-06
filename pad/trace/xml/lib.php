<?php


  function padTraceOccClose () {

      global $pad, $padTraceOccClose;

      if ( ! isset ( $padTraceOccClose ) or ! isset ( $padTraceOccClose [$pad] ) )
        return;

      if ( $padTraceOccClose [$pad] ) {
        $padTraceOccClose [$pad] = FALSE;
        padTraceXmlWrite ( '</occurs>', 'end' );
      }

  }

  function padTraceXml ( $trace, $info, $id, $type, $event ) {

    global $pad, $padTraceXmlLines, $padTraceOccClose;

    if ( ( $type == 'trace' or $type == 'level' or $type == 'occur' ) and 
         ( $event == 'start' or $event == 'end' ) )
      return;

    if ( $type == 'level' )
      padTraceOccClose ();

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

    global $pad, $padTag, $padTraceOccOpen, $padTraceOccClose, $padWalk, $padData;

    if ( $padWalk [$pad] == 'next' or count ( $padData [$pad] ) > 1 )
      $padTraceOccOpen [$pad] = TRUE;
    else 
      $padTraceOccOpen [$pad] = FALSE;

    $padTraceOccClose [$pad] = FALSE;

    $tag     = $padTag [$pad];
    $type    = $GLOBALS ['padTypeResult'] ?? '';
    $pair    = $GLOBALS ['padPairSet']    ?? '';
    $prmType = $GLOBALS ['padPrmTypeSet'] ?? '';

    $info = "<$tag" 
          . " level=\"$pad\""
          . " type=\"$type\""
          . " pair=\"$pair\""
          . " parms=\"$prmType\""
          . '>';

    padTraceXmlWrite ( $info, 'start' );
  
  }


  function padTraceXmlLevelEnd () {

    global $pad, $padTag;

    $tag = $padTag [$pad];

    padTraceOccClose ();
  
    padTraceXmlWrite ( "</$tag>", 'end' );

  }


  function padTraceXmlOccurStart () {

    global $pad, $padOccur, $padTraceXmlOcc, $padWalk, $padData, $padCurrent;

    $occur = $padOccur [$pad];

    if ( $padTraceXmlOcc ) 
      return padTraceXmlOccurStartGo ( $occur );

    if ( $padTraceXmlOccSmart ) 
      if ( $padWalk [$pad] <> 'next' or count ( $padCurrent [$pad] ) )
        return padTraceXmlOccurStartGo ( $occur );

  }


  function padTraceXmlOccurStartGo ( $occur ) {

    global $pad, $padOccur, $padTraceOccurWritten, $padTraceOccOpen, $padTraceOccClose;

    $occur = $padOccur [$pad];

    $padTraceOccurWritten [$pad] [$occur] = TRUE;

    if ( $padTraceOccOpen [$pad] ) {
      $padTraceOccOpen  [$pad] = FALSE;
      $padTraceOccClose [$pad] = TRUE;
      padTraceXmlWrite ( '<occurs>', 'start' );
    }

    padTraceXmlWrite ( "<occur nr=\"$occur\">", 'start' );

  }


  function padTraceXmlOccurEnd () {

    global $pad, $padOccur, $padTraceOccurWritten;

    $occur = $padOccur [$pad];

    if ( $padTraceOccurWritten [$pad] [$occur] );
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


  function padTraceXmlWrite ( $info, $action='' ) {

    global $padTraceBase, $padTraceSpaces, $padTraceActive;

    if ( $action == 'end' ) 
      $padTraceSpaces = $padTraceSpaces - 2;

    $spaces = str_repeat ( ' ', $padTraceSpaces );
    
    $padTraceActive = FALSE;
    padFilePutContents ( $padTraceBase . '/trace.xml', $spaces . $info, true );
    $padTraceActive = TRUE;

    if ( $action == 'start' ) 
      $padTraceSpaces = $padTraceSpaces + 2;
  
  }


?>