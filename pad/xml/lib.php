<?php


  function padXml () {

    global $padXmlEvents;

    foreach ( $padXmlEvents as $key => $event ) {

      if     ( $event ['event'] == 'level-start' ) padXmlLevel ( $event, 'start' );
      elseif ( $event ['event'] == 'level-end'   ) padXmlLevel ( $event, 'end'   );
      elseif ( $event ['event'] == 'occur-start' ) padXmlOccur ( $event, 'start' );
      elseif ( $event ['event'] == 'occur-end'   ) padXmlOccur ( $event, 'end'   );

    }

  }


  function padXmlLevel ( $event, $action ) {

    global $padXml;

    extract ( $event );
    extract ( $padXml [$tree] );

    $parms = [
      'size'   => $size,
      'result' => $result,
      'source' => $source,
      'parm'   => $parm,
      'type'   => $type
    ];

    padXmlGo ( $childs, $action, $tag, $parms );

  }


  function padXmlOccur ( $event, $action ) {

    global $padXml;

    extract ( $event );
    extract ( $padXml [$tree]  );
    extract ( $occurs  [$occur] );

    if ( count ($occurs) < 2)
      return;

    $parms = [
      'size' => $size,
      'id'   => $id,
      'type' => $type
    ];

    if ( $id == 1 and $action == 'start' )
      padXmlOpen ( 'occurs' );

    padXmlGo ( $childs, $action, 'occur', $parms );

    if ( $id == count ($occurs) and $action == 'end' )
      padXmlClose ( 'occurs' );

  }


  function padXmlGo ( $childs, $action, $tag, $parms ) {

    if ( ! $childs and $action == 'start')
      padXmlLine ( $tag, $parms );
    elseif ( $action == 'start' )
      padXmlOpen ( $tag, $parms );
    else
      padXmlClose ( $tag );

  }


  function padXmlOpen ( $xml, $parms=[] ) {
  
    $more = padXmlMore ( $parms );

    padXmlWrite ( "<$xml$more>" );
  
  }


  function padXmlLine ( $xml, $parms=[] ) {
  
    $more = padXmlMore ( $parms );
 
    padXmlWrite ( "<$xml$more/>" );
  
  }


  function padXmlClose ( $xml ) {
  
    padXmlWrite ( "</$xml>" );
  
  }


  function padXmlWrite ( $xml ) {
  
    global $padXmlFile;

    padFilePutContents ( $padXmlFile, $xml, true );
  
  }


  function padXmlMore ( $parms ) {
  
    $more = '';

    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . htmlspecialchars($value) . '"';

    return $more;
 
    padXmlWrite ( "<$xml$more/>" );
  
  }


  function padXmlTidy () {

    global $padXmlFile;

    $options = [
      'input-xml'           => true,
      'output-xml'          => true,
      'force-output'        => true,
      'add-xml-decl'        => false,
      'indent'              => true,
      'tab-size'            => 2,
      'indent-spaces'       => 2,
      'vertical-space'      => 'no',
      'wrap'                => 0,
      'clean'               => 'yes',
      'drop-empty-elements' => 'yes'
    ];

    $tidy = new tidy;
    $tidy->parseString ( padFileGetContents ( padData . $padXmlFile ), $options, 'utf8' );
    $tidy->cleanRepair();

    if ( $tidy === FALSE )
      return padError ( "TIDY conversion error");

    padFilePutContents ( $padXmlFile , $tidy->value );

  }
 

  function padCounter ( $file ) {

    if ( ! file_exists ( padData . $file) )
      padFilePutContents ( $file , '0' );

    $now = padFileGetContents ( padData . $file );

    $now ++;

    padFilePutContents ( $file , $now );  

  }


?>