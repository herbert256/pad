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
    if ( $padXmlDetails       ) $options ['id'] = $tree;

    if ( $padXmlTree [$tree] ['childs'] )
      padXmlOpen ( $tag, $options );
    else
      padXmlLine ( $tag, $options );

    if ( $count > 1 )
      padXmlLevelParms ( $parms );
   
  }


  function padXmlLevelParms ( $parms ) {  

    padXmlOpen ( 'parms' );

    foreach ( $parms as $type => $list )
      foreach ( $list as $name => $value )
        padXmlLine ( "parm type=\"$type\" name=\"$name\" value=\"" .  htmlentities($value). '"' );

     padXmlClose ( 'parms' );

  }

  function padXmlLevelEnd ( $event ) {

    global $padXmlTree;

    extract ( $event );
    extract ( $padXmlTree [$tree] );

    if ( $written ) padXmlClose ( 'occurs' );
    if ( $childs  ) padXmlClose ( $tag );

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

    if ( $id == 1 ) {
      padXmlOpen ( 'occurs' );
      $padXmlTree [$tree] ['written'] = TRUE;
    }

    if ( $childs )
      padXmlOpen ( 'occur', $parms );
    else
      padXmlLine ( 'occur', $parms );

  }


  function padXmlOccurEnd ( $event ) {

    global $padXmlTree;

    extract ( $event );
    extract ( $padXmlTree [$tree]  );
    extract ( $occurs  [$occur] );

    if ( count ($occurs) < 2)
      return;

    if ( $childs )
     padXmlClose ( 'occur' );

  }  


  function padXmlOpen ( $xml, $parms=[] ) {
  
    global $padXmlDepth;

    $more = padXmlMore ( $parms );

    padXmlWrite ( "<$xml$more>" );

    $padXmlDepth++;
      
  }


  function padXmlLine ( $xml, $parms=[] ) {
  
    $more = padXmlMore ( $parms );
 
    padXmlWrite ( "<$xml$more />" );
  
  }


  function padXmlClose ( $xml ) {

    global $padXmlDepth;

    $padXmlDepth--;
  
    padXmlWrite ( "</$xml>" );
  
  }


  function padXmlWrite ( $xml ) {
  
    global $padInfoDir, $padXmlDetails, $padInfoId, $padXmlDepth;

    if ( $padXmlDepth > 0 )
      $spaces = str_repeat ( ' ', $padXmlDepth * 2 );
    else
      $spaces = '';

    if ( $padXmlDetails )
      padInfoPut ( "$padInfoDir/xml/tree.xml", "$spaces$xml", true );
    else
      padInfoPut ( "$padInfoDir/tree.xml", "$spaces$xml", true );
  
  }


  function padXmlMore ( $parms ) {
  
    $more = '';

    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . str_replace ( '&#039;', "'", htmlspecialchars ($value) ) . '"';

    return $more;
  
  }


  function padXmlTidy () {

    global $padInfoDir, $padXmlDetails, $padInfoId, $padXmlTidy;

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
      $data = padInfoGet ( padData . "$padInfoDir/xml/tree.xml" );
    else
      $data = padInfoGet ( padData . "$padInfoDir/tree.xml" );

    $tidy = new tidy;
    $tidy->parseString ( $data, $options, 'utf8' );
    $tidy->cleanRepair();

    if ( $tidy === FALSE )
      return padError ( "TIDY conversion error");

    if ( $padXmlDetails )
      $data = padInfoPut ( "$padInfoDir/xml/tree-tidy.xml", $tidy->value );
    else
      $data = padInfoPut ( "$padInfoDir/tree-tidy.xml", $tidy->value );

  }


?>