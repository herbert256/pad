<?php

  function padInfoXml () {

    global $padInfoXmlEvents, $padInfoXmlFile;

    foreach ( $padInfoXmlEvents as $event ) {

      if     ( $event ['event'] == 'level-start' ) padInfoXmlLevelStart ( $event );
      elseif ( $event ['event'] == 'level-end'   ) padInfoXmlLevelEnd   ( $event );
      elseif ( $event ['event'] == 'occur-start' ) padInfoXmlOccurStart ( $event );
      elseif ( $event ['event'] == 'occur-end'   ) padInfoXmlOccurEnd   ( $event );

    }

  }

  function padInfoXmlLevelStart ( $event ) {

    global $padInfoXmlTree, $padInfoXmlCompact;

    extract ( $event );
    extract ( $padInfoXmlTree [$tree] );

    if ( ! $size and $padInfoXmlCompact ) {
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

    if ( $count > 1 and ! $padInfoXmlCompact )
      $padInfoXmlTree [$tree] ['childs'] = TRUE;
    else
      $options ['parm'] = $parm;

    if ( $type   <> 'pad'     ) $options ['type']   = $type;
    if ( $source <> 'content' ) $options ['source'] = $source;

    if ( $padInfoXmlCompact )
      $options = [];

    if ( $padInfoXmlTree [$tree] ['childs'] )
      padInfoXmlOpen ( $tag, $options );
    else
      padInfoXmlLine ( $tag, $options );

    if ( $count > 1 and ! $padInfoXmlCompact )
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

    if ( $written )
      padInfoXmlClose ( 'occurs' );

    if ( $padInfoXmlTree [$tree] ['childs'] )
      padInfoXmlClose ( $tag );

  }

  function padInfoXmlOccurStart ( $event ) {

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
      padInfoXmlOpen ( 'occurs' );
      $padInfoXmlTree [$tree] ['written'] = TRUE;
    }

    if ( $childs )
      padInfoXmlOpen ( 'occur', $parms );
    else
      padInfoXmlLine ( 'occur', $parms );

  }

  function padInfoXmlOccurEnd ( $event ) {

    global $padInfoXmlTree, $padInfoXmlCompact;

    if ( $padInfoXmlCompact )
      return;

    extract ( $event );
    extract ( $padInfoXmlTree [$tree]  );
    extract ( $occurs  [$occur] );

    if ( count ($occurs) < 2)
      return;

    if ( $childs )
     padInfoXmlClose ( 'occur' );

  }

  function padInfoXmlOpen ( $xml, $parms=[] ) {

    global $padInfoXmlDepth;

    $more = padInfoXmlMore ( $parms );

    padInfoXmlWrite ( "<$xml$more>" );

    $padInfoXmlDepth++;

  }

  function padInfoXmlLine ( $xml, $parms=[] ) {

    $more = padInfoXmlMore ( $parms );

    padInfoXmlWrite ( "<$xml$more />" );

  }

  function padInfoXmlClose ( $xml ) {

    global $padInfoXmlDepth;

    $padInfoXmlDepth--;

    padInfoXmlWrite ( "</$xml>" );

  }

  function padInfoXmlWrite ( $xml ) {

    global $padInfoXmlDepth, $padInfoXmlFile;

    if ( $padInfoXmlDepth > 0 )
      $spaces = str_repeat ( ' ', $padInfoXmlDepth * 2 );
    else
      $spaces = '';

    padFilePut ( $padInfoXmlFile, "$spaces$xml", 1 );

  }

  function padInfoXmlMore ( $parms ) {

    $more = '';

    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . str_replace ( '&#039;', "'", htmlspecialchars ($value) ) . '"';

    return $more;

  }

?>