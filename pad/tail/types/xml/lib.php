<?php


  function padXml () {

    global $padXmlEvents;

    foreach ( $padXmlEvents as $key => $event ) {

      if     ( $event ['event'] == 'level-start' ) padXmlLevelStart ( $event );
      elseif ( $event ['event'] == 'level-end'   ) padXmlLevelEnd   ( $event );
      elseif ( $event ['event'] == 'occur-start' ) padXmlOccurStart ( $event );
      elseif ( $event ['event'] == 'occur-end'   ) padXmlOccurEnd   ( $event );

    }

  }


  function padXmlLevelStart ( $event ) {

    global $padXmlTree, $padXmlDetails;

    extract ( $event );
    extract ( $padXmlTree [$tree] );

    $count = 0;
    foreach ( $parms as $list )
      foreach ( $list as $value )
        $count++;

    $options            = [];
    $options ['size']   = $size;
    $options ['result'] = $result;

    if ( $count > 1 ) 
      $padXmlTree [$tree] ['childs'] = TRUE;
    else
      $options ['parm'] = $parm;
    
    if ( $type   <> 'pad'     ) $options ['type']   = $type;
    if ( $source <> 'content' ) $options ['source'] = $source;

    if ( $padXmlDetails ) {
      $options ['id']        = $tree;
      $options ['parent']    = $parent;
      $options ['parentOcc'] = $parentOcc;
    }

    if ( $padXmlTree [$tree] ['childs'] )
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

    global $padXmlTree;

    extract ( $event );
    extract ( $padXmlTree [$tree] );

    if ( $childs )
      padXmlClose ( $tag );

  }


  function padXmlOccurStart ( $event ) {

    global $padXmlTree;

    extract ( $event );
    extract ( $padXmlTree [$tree]  );
    extract ( $occurs  [$occur] );

    if ( count ($occurs) < 2)
      return;

    $parms = [
      'size' => $size,
      'id'   => $id,
      'type' => $type
    ];

    if ( $id == 1 )
      padXmlOpen ( 'occurs' );

    if ( ! $childs )
      padXmlLine ( 'occur', $parms );
    else
      padXmlOpen ( 'occur', $parms );

  }



  function padXmlOccurEnd ( $event ) {

    global $padXmlTree;

    extract ( $event );
    extract ( $padXmlTree [$tree]  );
    extract ( $occurs  [$occur] );

    if ( count ($occurs) < 2)
      return;

    padXmlClose ( 'occur' );

    if ( $id == count ($occurs) )
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
  
    global $padXmlDir, $padXmlDetails, $padTailId;

    if ( $padXmlDetails )
      padTailPut ( "$padXmlDir/$padTailId/base.xml", $xml, true );
    else
      padTailPut ( "$padXmlDir/$padTailId.xml", $xml, true );
  
  }


  function padXmlMore ( $parms ) {
  
    $more = '';

    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . htmlspecialchars($value) . '"';

    return $more;
  
  }


  function padXmlTidy () {

    global $padXmlDir, $padXmlDetails, $padTailId, $padXmlTidy;

    if ( ! $padXmlTidy )
      return;

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

    if ( $padXmlDetails )
      $data = padTailGet ( padData . "$padXmlDir/$padTailId/base.xml" );
    else
      $data = padTailGet ( padData . "$padXmlDir/$padTailId.xml" );

    $tidy = new tidy;
    $tidy->parseString ( $data, $options, 'utf8' );
    $tidy->cleanRepair();

    if ( $tidy === FALSE )
      return padError ( "TIDY conversion error");

    if ( $padXmlDetails )
      $data = padTailPut ( "$padXmlDir/$padTailId/tidy.xml", $tidy->value );
    else
      $data = padTailPut ( "$padXmlDir/$padTailId.xml", $tidy->value );

  }


?>