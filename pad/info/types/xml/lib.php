<?php


  function padXml () {

    global $padXmlEvents;

    foreach ( $padXmlEvents as $key => $event ) {

      if     ( $event ['event'] == 'level-start' ) padXmlLevelStart ( $event );
      elseif ( $event ['event'] == 'level-end'   ) padXmlLevelEnd   ( $event );
      elseif ( $event ['event'] == 'occur-start' ) padXmlOccurStart ( $event );
      elseif ( $event ['event'] == 'occur-end'   ) padXmlOccurEnd   ( $event );

      if     ( $event ['event'] == 'level-start' ) padXmlLevelStartShort ( $event );
      elseif ( $event ['event'] == 'level-end'   ) padXmlLevelEndShort   ( $event );
  
    }

  }


  function padXmlLevelStart ( $event ) {

    global $padXmlTree, $padXmlShowEmpty;

    extract ( $event );
    extract ( $padXmlTree [$tree] );

    if ( ! $size and ! $padXmlShowEmpty ) {
      $padXmlTree [$tree] ['SKIP'] = TRUE;
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
      $padXmlTree [$tree] ['childs'] = TRUE;
    else
      $options ['parm'] = $parm;
    
    if ( $type   <> 'pad'     ) $options ['type']   = $type;
    if ( $source <> 'content' ) $options ['source'] = $source;

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

    if ( isset ($padXmlTree [$tree] ['SKIP'] ) )
      return;

    if ( $written ) padXmlClose ( 'occurs' );
    if ( $childs  ) padXmlClose ( $tag, 'short' );

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


  function padXmlLevelStartShort ( $event ) {

    global $padXmlTree;

    extract ( $event );
    extract ( $padXmlTree [$tree] );

    $options = [];

    if ( $size               ) $options ['size']   = $size;
    if ( count ($occurs) > 1 ) $options ['occurs'] = count ($occurs);
    if ( $parm               ) $options ['parm']   = $parm;

    if ( $padXmlTree [$tree] ['childs'] )
      padXmlOpen ( $tag, $options, 'short' );
    else
      padXmlLine ( $tag, $options, 'short' );
   
  }


  function padXmlLevelEndShort ( $event ) {

    global $padXmlTree;

    extract ( $event );
    extract ( $padXmlTree [$tree] );

    if ( $childs ) 
      padXmlClose ( $tag );

  }


  function padXmlOpen ( $xml, $parms=[], $type='long' ) {
  
    global $padXmlDepth;

    $more = padXmlMore ( $parms );

    padXmlWrite ( "<$xml$more>", $type );

    $padXmlDepth++;
      
  }


  function padXmlLine ( $xml, $parms=[], $type='long' ) {
  
    $more = padXmlMore ( $parms );
 
    padXmlWrite ( "<$xml$more />", $type );
  
  }


  function padXmlClose ( $xml, $type='long' ) {

    global $padXmlDepth;

    $padXmlDepth--;
  
    padXmlWrite ( "</$xml>", $type );
  
  }


  function padXmlWrite ( $xml, $type='long' ) {
  
    global $padXmlDepth, $padXmlDir;

    if ( $padXmlDepth > 0 )
      $spaces = str_repeat ( ' ', $padXmlDepth * 2 );
    else
      $spaces = '';

    padInfoLine ( "$padXmlDir/$type.xml", "$spaces$xml" );
  
  }


  function padXmlMore ( $parms ) {
  
    $more = '';

    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . str_replace ( '&#039;', "'", htmlspecialchars ($value) ) . '"';

    return $more;
  
  }


  function padXmlTidy () {

    global $padXmlTidy, $padXmlDir;

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

    padXmlTidyGo ( "$padXmlDir/long.xml",  $options );
    padXmlTidyGo ( "$padXmlDir/short.xml", $options );

  }


  function padXmlTidyGo ( $file, $options ) {

    $data = padInfoGet ( padData . $file );

    $tidy = new tidy;
    $tidy->parseString ( $data, $options, 'utf8' );
    $tidy->cleanRepair();

    if ( $tidy === FALSE )
      return;

    $data = padInfoFile ( $file, $tidy->value );

  }


?>