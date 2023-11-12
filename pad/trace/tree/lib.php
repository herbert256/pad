<?php

  function padTreePrint () {

    global $padTreeFile;

    padFilePutContents ( "$padTreeFile/tree.json",   $GLOBALS ['padTree'],   true );
    padFilePutContents ( "$padTreeFile/events.json", $GLOBALS ['padEvents'], true );

    foreach ( $GLOBALS ['padEvents'] as $key => $event ) {

      if     ( $event ['event_type'] == 'level-start' ) padTreePrintLevel ( $event, 'start' );
      elseif ( $event ['event_type'] == 'level-end'   ) padTreePrintLevel ( $event, 'end'   );
      elseif ( $event ['event_type'] == 'occur-start' ) padTreePrintOccur ( $event, 'start' );
      elseif ( $event ['event_type'] == 'occur-end'   ) padTreePrintOccur ( $event, 'end'   );

    }

  }


  function padTreePrintLevel ( $event, $action ) {

    global $padTree;

    extract ( $event );
    extract ( $padTree [$event_tree] );

    $count = count ($occurs);

    $parms = [
      'size'   => $size,
      'result' => $result,
      'source' => $source,
      'parm'   => $parm,
      'type'   => $type
    ];

    if ( ! $childs and $action == 'start')
      padTreeWriteLine ( $tag, $parms );
    elseif ( $action == 'start' )
      padTreeWriteOpen ( $tag, $parms );
    else
      padTreeWriteClose ( $tag );

  }


  function padTreePrintOccur ( $event, $action ) {

    global $padTree;

    extract ( $event );
    extract ( $padTree [$event_tree] );

    if ( count ($occurs) < 2)
      return;

    extract ( $occurs [$event_occur] );

    $parms = [
      'size' => $size,
      'id'   => $id,
      'type' => $type
    ];

    if ( $id == 1 and $action == 'start' )
      padTreeWriteOpen ( 'occurs' );

    if ( ! $childs and $action == 'start' ) 
      padTreeWriteLine ( 'occur', $parms );
    elseif ( $action == 'start' )
      padTreeWriteOpen ( 'occur', $parms );
    else
      padTreeWriteClose ( 'occur' );

    if ( $id == count ($occurs) and $action == 'end' )
      padTreeWriteClose ( 'occurs' );

  }


  function padTreeWriteOpen ( $xml, $parms=[] ) {
  
    $more = padTreeMore ( $parms );

    padTreeWrite ( "<$xml$more>" );
  
  }


  function padTreeWriteLine ( $xml, $parms=[] ) {
  
    $more = padTreeMore ( $parms );
 
    padTreeWrite ( "<$xml$more/>" );
  
  }


  function padTreeWriteClose ( $xml ) {
  
    padTreeWrite ( "</$xml>" );
  
  }


  function padTreeMore ( $parms ) {
  
    $more = '';

    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . htmlspecialchars($value) . '"';

    return $more;
 
    padTreeWrite ( "<$xml$more/>" );
  
  }


  function padTreeWrite ( $xml ) {
  
    global $padTreeFile;

    padFilePutContents ( "$padTreeFile/tree.xml", $xml, true );
  
  }


?>