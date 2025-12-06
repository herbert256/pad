<?php


  function padInfoJson () {

    global $padInfoXmlEvents, $padInfoXmlFile;

    foreach ( $padInfoXmlEvents as $event ) {

      if     ( $event ['event'] == 'level-start' ) padInfoJsonLevelStart ( $event );
      elseif ( $event ['event'] == 'occur-start' ) padInfoJsonOccurStart ( $event );

    }

  }


  function padInfoJsonLevelStart ( $event ) {

    global $padInfoXmlTree, $padInfoXmlCompact;

    extract ( $event );
    extract ( $padInfoXmlTree [$tree] );

    if ( $padInfoXmlTree [$tree] ['SKIP'] )
      return;

    $count = 0;
    foreach ( $parms as $list )
      foreach ( $list as $value )
        $count++;

    $options            = [];
    $options ['size']   = $size;
    $options ['result'] = $result;

    if ( $count <= 1 or $padInfoXmlCompact ) 
      $options ['parm'] = $parm;
    
    if ( $type   <> 'pad'     ) $options ['type']   = $type;
    if ( $source <> 'content' ) $options ['source'] = $source;

    if ( $padInfoXmlCompact )
      $options = [];

    if ( $padInfoXmlTree [$tree] ['childs'] )
      padInfoJsonOpen ( $tag, $options );
    else
      padInfoJsonLine ( $tag, $options );

    if ( $count > 1 and ! $padInfoXmlCompact )
      padInfoJsonLevelParms ( $parms );
   
  }


  function padInfoJsonLevelParms ( $parms ) {  

    padInfoJsonOpen ( 'parms' );

    foreach ( $parms as $type => $list )
      foreach ( $list as $name => $value )
        padInfoJsonLine ( "parm type=\"$type\" name=\"$name\" value=\"" .  htmlentities($value). '"' );

     padInfoJsonClose ( 'parms' );

  }


  function padInfoJsonOccurStart ( $event ) {

    global $padInfoXmlTree, $padInfoXmlCompact;

    if ( $padInfoXmlCompact ) 
      return;

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
      padInfoJsonOpen ( 'occurs' );
      $padInfoXmlTree [$tree] ['written'] = TRUE;
    }

    if ( $childs )
      padInfoJsonOpen ( 'occur', $parms );
    else
      padInfoJsonLine ( 'occur', $parms );

  }


  function padInfoJsonOpen ( $xml, $parms=[] ) {
  
    global $padInfoXmlDepth;

    $more = padInfoJsonMore ( $parms );

    padInfoJsonWrite ( "<$xml$more>" );

    $padInfoXmlDepth++;
      
  }


  function padInfoJsonLine ( $xml, $parms=[] ) {
  
    $more = padInfoJsonMore ( $parms );
 
    padInfoJsonWrite ( "<$xml$more />" );
  
  }


  function padInfoJsonWrite ( $xml ) {
  
    global $padInfoXmlDepth, $padInfoXmlFile;

    if ( $padInfoXmlDepth > 0 )
      $spaces = str_repeat ( ' ', $padInfoXmlDepth * 2 );
    else
      $spaces = '';

    padFilePut ( $padInfoXmlFile, "$spaces$xml", 1 );
  
  }


  function padInfoJsonMore ( $parms ) {
  
    $more = '';

    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . str_replace ( '&#039;', "'", htmlspecialchars ($value) ) . '"';

    return $more;
  
  }


?>