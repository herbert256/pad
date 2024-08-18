<?php


  function padInfoXml () {

    global $padInfoXmlEvents;

    foreach ( $padInfoXmlEvents as $key => $event ) {

      if     ( $event ['event'] == 'level-start' ) padInfoXmlLevelStart ( $event );
      elseif ( $event ['event'] == 'level-end'   ) padInfoXmlLevelEnd   ( $event );
      elseif ( $event ['event'] == 'occur-start' ) padInfoXmlOccurStart ( $event );
      elseif ( $event ['event'] == 'occur-end'   ) padInfoXmlOccurEnd   ( $event );

      if     ( $event ['event'] == 'level-start' ) padInfoXmlLevelStartShort ( $event );
      elseif ( $event ['event'] == 'level-end'   ) padInfoXmlLevelEndShort   ( $event );
  
    }

  }


  function padInfoXmlLevelStart ( $event ) {

    global $padInfoXmlTree, $padInfoXmlShowEmpty;

    extract ( $event );
    extract ( $padInfoXmlTree [$tree] );

    if ( ! $size and ! $padInfoXmlShowEmpty ) {
      $padInfoXmlTree [$tree] ['SKIP'] = TRUE;
      return;
    }

    $count = 0;
    foreach ( $parms as $list )
      foreach ( $list as $value )
        $count++;

    $options            = [];
    $options ['size']   = $size;
    $options ['result'] = $result;

    if ( $count > 1 ) 
      $padInfoXmlTree [$tree] ['childs'] = TRUE;
    else
      $options ['parm'] = $parm;
    
    if ( $type   <> 'pad'     ) $options ['type']   = $type;
    if ( $source <> 'content' ) $options ['source'] = $source;

    if ( $padInfoXmlTree [$tree] ['childs'] )
      padInfoXmlOpen ( $tag, $options );
    else
      padInfoXmlLine ( $tag, $options );

    if ( $count > 1 )
      padInfoXmlLevelParms ( $parms );
   
  }


  function padInfoXmlLevelParms ( $parms ) {  

    padInfoXmlOpen ( 'parms' );

    foreach ( $parms as $type => $list )
      foreach ( $list as $name => $value )
        padInfoXmlLine ( "parm type=\"$type\" name=\"$name\" value=\"" .  htmlentities($value). '"' );

     padInfoXmlClose ( 'parms' );

  }


  function padInfoXmlLevelEnd ( $event ) {

    global $padInfoXmlTree;

    extract ( $event );
    extract ( $padInfoXmlTree [$tree] );

    if ( isset ($padInfoXmlTree [$tree] ['SKIP'] ) )
      return;

    if ( $written ) padInfoXmlClose ( 'occurs' );
    if ( $childs  ) padInfoXmlClose ( $tag, 'short' );

  }


  function padInfoXmlOccurStart ( $event ) {

    global $padInfoXmlTree;

    extract ( $event );
    extract ( $padInfoXmlTree [$tree]  );
    extract ( $occurs  [$occur] );

    if ( count ($occurs) < 2)
      return;

    $parms = [
      'size' => $size,
      'id'   => $id,
      'type' => $type
    ];

    if ( $id == 1 ) {
      padInfoXmlOpen ( 'occurs' );
      $padInfoXmlTree [$tree] ['written'] = TRUE;
    }

    if ( $childs )
      padInfoXmlOpen ( 'occur', $parms );
    else
      padInfoXmlLine ( 'occur', $parms );

  }


  function padInfoXmlOccurEnd ( $event ) {

    global $padInfoXmlTree;

    extract ( $event );
    extract ( $padInfoXmlTree [$tree]  );
    extract ( $occurs  [$occur] );

    if ( count ($occurs) < 2)
      return;

    if ( $childs )
     padInfoXmlClose ( 'occur' );

  }  


  function padInfoXmlLevelStartShort ( $event ) {

    global $padInfoXmlTree;

    extract ( $event );
    extract ( $padInfoXmlTree [$tree] );

    $options = [];

    if ( $size               ) $options ['size']   = $size;
    if ( count ($occurs) > 1 ) $options ['occurs'] = count ($occurs);
    if ( $parm               ) $options ['parm']   = $parm;

    if ( $padInfoXmlTree [$tree] ['childs'] )
      padInfoXmlOpen ( $tag, $options, 'short' );
    else
      padInfoXmlLine ( $tag, $options, 'short' );
   
  }


  function padInfoXmlLevelEndShort ( $event ) {

    global $padInfoXmlTree;

    extract ( $event );
    extract ( $padInfoXmlTree [$tree] );

    if ( $childs ) 
      padInfoXmlClose ( $tag );

  }


  function padInfoXmlOpen ( $xml, $parms=[], $type='long' ) {
  
    global $padInfoXmlDepth;

    $more = padInfoXmlMore ( $parms );

    padInfoXmlWrite ( "<$xml$more>", $type );

    $padInfoXmlDepth++;
      
  }


  function padInfoXmlLine ( $xml, $parms=[], $type='long' ) {
  
    $more = padInfoXmlMore ( $parms );
 
    padInfoXmlWrite ( "<$xml$more />", $type );
  
  }


  function padInfoXmlClose ( $xml, $type='long' ) {

    global $padInfoXmlDepth;

    $padInfoXmlDepth--;
  
    padInfoXmlWrite ( "</$xml>", $type );
  
  }


  function padInfoXmlWrite ( $xml, $type='long' ) {
  
    global $padInfoXmlDepth, $padInfoXmlDir;

    if ( $padInfoXmlDepth > 0 )
      $spaces = str_repeat ( ' ', $padInfoXmlDepth * 2 );
    else
      $spaces = '';

    padInfoLine ( "$padInfoXmlDir/$type.xml", "$spaces$xml" );
  
  }


  function padInfoXmlMore ( $parms ) {
  
    $more = '';

    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . str_replace ( '&#039;', "'", htmlspecialchars ($value) ) . '"';

    return $more;
  
  }


  function padInfoXmlTidy () {

    global $padInfoXmlTidy, $padInfoXmlDir;

    if ( ! $padInfoXmlTidy )
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

    padInfoXmlTidyGo ( "$padInfoXmlDir/long.xml",  $options );
    padInfoXmlTidyGo ( "$padInfoXmlDir/short.xml", $options );

  }


  function padInfoXmlTidyGo ( $file, $options ) {

    $data = padInfoGet ( '/data/' . $file );

    $tidy = new tidy;
    $tidy->parseString ( $data, $options, 'utf8' );
    $tidy->cleanRepair();

    if ( $tidy === FALSE )
      return;

    $value = $tidy->value;

    if ( $value and strlen($value) > 10 )
      padInfoFile ( $file, $value );

  }


?>