<?php


  function padXml () {

    global $padXmlEvents;

    foreach ( $padXmlEvents as $key => $event ) {

      if     ( $event ['event'] == 'level-start' ) padXmlLevelStart ( $event );
      elseif ( $event ['event'] == 'level-end'   ) padXmlLevelEnd   ( $event );
      elseif ( $event ['event'] == 'occur-start' ) padXmlOccur ( $event, 'start' );
      elseif ( $event ['event'] == 'occur-end'   ) padXmlOccur ( $event, 'end'   );

    }

  }


  function padXmlLevelStart ( $event ) {

    global $padXml;

    extract ( $event );
    extract ( $padXml [$tree] );

    $count = 0;
    foreach ( $parms as $list )
      foreach ( $list as $value )
        $count++;

    $options            = [];
    $options ['size']   = $size;
    $options ['result'] = $result;

    if ( $count > 1 ) 
      $padXml [$tree] ['childs'] = TRUE;
    else
      $options ['parm'] = $parm;
    
    if ( $type   <> 'pad'     ) $options ['type']   = $type;
    if ( $source <> 'content' ) $options ['source'] = $source;

    $options ['id'] = $tree;

    if ( $padXml [$tree] ['childs'] )
      padXmlOpen ( $tag, $options );
    else
      padXmlLine ( $tag, $options );

    if ( $count > 1 ) {

      padXmlOpen ( 'parms' );

      foreach ( $parms as $type => $list )
        foreach ( $list as $name => $value )
          padXmlLine ( "parm type=\"$type\" name=\"$name\" value=\"" .  htmlentities($value). '"' );

       padXmlClose ( 'parms' );
   
    }

  }

  function padXmlLevelEnd ( $event ) {

    global $padXml;

    extract ( $event );
    extract ( $padXml [$tree] );

    if ( $childs )
      padXmlClose ( $tag );

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

    if ( ! $childs and $action == 'start')
      padXmlLine ( 'occur', $parms );
    elseif ( $action == 'start' )
      padXmlOpen ( 'occur', $parms );
    else
      padXmlClose ( 'occur' );

    if ( $id == count ($occurs) and $action == 'end' )
      padXmlClose ( 'occurs' );

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

    padFilePutContents ( "$padXmlFile/tree.xml", $xml, true );
  
  }


  function padXmlMore ( $parms ) {
  
    $more = '';

    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . htmlspecialchars($value) . '"';

    return $more;
  
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

    $data = padFileGetContents ( padData . "$padXmlFile/tree.xml" );

    $tidy = new tidy;
    $tidy->parseString ( $data, $options, 'utf8' );
    $tidy->cleanRepair();

    if ( $tidy === FALSE )
      return padError ( "TIDY conversion error");

    padFilePutContents ( "$padXmlFile/tidy.xml" , $tidy->value );

  }
 

  function padCounter ( $file ) {

    if ( ! $file )
      return;

    if ( ! file_exists ( padData . "/counters/$file" ) )
      padFilePutContents ( $file , '0' );

    $now = padFileGetContents ( padData . "/counters/$file" );

    $now ++;

    padFilePutContents ( "/counters/$file" , $now );  

  }


?>