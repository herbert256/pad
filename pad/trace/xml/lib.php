<?php


  function padTraceXml ( $trace, $info, $id, $type, $event ) {

    global $padTraceXmlLines;

    if ( ( $type == 'trace' or $type == 'level' or $type == 'occur' ) 
                            and 
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

    global $pad, $padTag, $padOccur, $padTraceXmlOcc, $padTraceXmlOccSmart, $padTraceOccurTag;

    if ( $type == 'level' ) $tag   = $padTag [$pad];
    if ( $type == 'occur' ) $occur = $padOccur [$pad];

    if ( $type == 'occur' and $event == 'start' )
      $padTraceOccurTag [$pad] [$occur] = ( $padTraceXmlOcc or ( $padTraceXmlOccSmart and ! padDefaultOccur () ) );

    if ( $type == 'occur' )
      $go = $padTraceOccurTag [$pad] [$occur];

    if     ( $type == 'trace' and $event == 'start'         ) $info = '<trace>';
    elseif ( $type == 'trace' and $event == 'end'           ) $info = '</trace>';
    elseif ( $type == 'level' and $event == 'start'         ) $info = "<$tag level=\"$pad\">";
    elseif ( $type == 'level' and $event == 'end'           ) $info = "</$tag>";
    elseif ( $type == 'occur' and $event == 'start' and $go ) $info = "<occur nr=\"$occur\">";
    elseif ( $type == 'occur' and $event == 'end'   and $go ) $info = "</occur>";

    else return;

    padTraceXmlWrite ( $info, $event );

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
 
    if ( strlen ( $info ) > 30 ) {

      $extra = " more=\"$padTraceLine\" ";

      $file = "$padTraceBase/more/$padTraceLine.txt";

      if ( ! padExists ( padData . $file ) ) 
        padFilePutContents ( $file, $info );

      $info = substr ( $info, 0, 30 );
   
    }

    $info = htmlspecialchars ( $info );
    
    padTraceXmlWrite ( "<$xml value=\"$info\"$extra/>" );

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