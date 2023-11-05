<?php


  function padTraceXml ( $xml, $id, $type, $event ) {

    global $padTraceXmlLines;

    if ( ( $type == 'trace' or $type == 'level' or $type == 'occur' ) 
                            and 
         ( $event == 'start' or $event == 'end' ) )
      return;

    if ( $event == 'start' )
      padTraceXmlWrite ( "<$type>", 'start' );

    if ( $padTraceXmlLines )
      padTraceXmlLine ( $xml, $type, $event, $id );

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

    if     ( $type == 'trace' and $event == 'start'         ) $xml = '<trace>';
    elseif ( $type == 'trace' and $event == 'end'           ) $xml = '</trace>';
    elseif ( $type == 'level' and $event == 'start'         ) $xml = "<$tag>";
    elseif ( $type == 'level' and $event == 'end'           ) $xml = "</$tag>";
    elseif ( $type == 'occur' and $event == 'start' and $go ) $xml = "<occur-$occur>";
    elseif ( $type == 'occur' and $event == 'end'   and $go ) $xml = "</occur-$occur>";

    else return;

    padTraceXmlWrite ( $xml, $event );

  }


  function padTraceXmlLine ( $xml, $type, $event, $id ) {

    global $padTraceXmlNoEmpty;

    if ( ! $xml and $padTraceXmlNoEmpty )
      return;

    if ( $event == 'start' or $event == 'end' ) {

      if ( $xml )
        padTraceXmlLineInParent ( $xml, $event );

    } else {

      if ( $id )
        padTraceXmlLineInParent ( $xml, $event );
      else
        padTraceXmlIndependedLine ( $xml, $type, $event);

    }

  }


  function padTraceXmlIndependedLine ( $xml, $type, $event ) {

    global $padTraceLine, $padTraceXmlLines;

    $id = padTraceXmlId ( $xml );

    $xml = htmlspecialchars ( $xml );
    $xml = "<$type event=\"$event\" value=\"$xml\" $id/>";      
 
    padTraceXmlWrite ( $xml );

  }


  function padTraceXmlLineInParent ( $xml, $event ) {

    global $padTraceLine ;

    $id = padTraceXmlId ( $xml );

    $xml = htmlspecialchars ( $xml );
    $xml = "<$event value=\"$xml\" $id/>";

    padTraceXmlWrite ( $xml );

  }


  function padTraceXmlWrite ( $xml, $action='' ) {

    global $padTraceBase, $padTraceSpaces, $padTraceActive;

    if ( $action == 'end' ) 
      $padTraceSpaces = $padTraceSpaces - 2;

    $spaces = str_repeat ( ' ', $padTraceSpaces );
    
    $padTraceActive = FALSE;
    padFilePutContents ( $padTraceBase . '/trace.xml', $spaces . $xml, true );
    $padTraceActive = TRUE;

    if ( $action == 'start' ) 
      $padTraceSpaces = $padTraceSpaces + 2;
  
  }


  function padTraceXmlId ( $xml ) {

    global $padTraceLine, $padTraceXmlId;

    if ( $padTraceXmlId or str_ends_with( ' <more>', $xml) )
      return "id=\"$padTraceLine\"";
    else
      return '';
  
    return $xml;

  }




?>