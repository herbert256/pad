<?php


  function padTree () {

    global $padEvents;

    foreach ( $padEvents as $key => $event ) {

      if     ( $event ['event'] == 'level-start' ) padTreeLevel ( $event, 'start' );
      elseif ( $event ['event'] == 'level-end'   ) padTreeLevel ( $event, 'end'   );
      elseif ( $event ['event'] == 'occur-start' ) padTreeOccur ( $event, 'start' );
      elseif ( $event ['event'] == 'occur-end'   ) padTreeOccur ( $event, 'end'   );

    }

  }


  function padTreeLevel ( $event, $action ) {

    global $padTree;

    extract ( $event );
    extract ( $padTree [$tree] );

    $parms = [
      'size'   => $size,
      'result' => $result,
      'source' => $source,
      'parm'   => $parm,
      'type'   => $type
    ];

    padTreeGo ( $childs, $action, $tag, $parms );

  }


  function padTreeOccur ( $event, $action ) {

    global $padTree;

    extract ( $event );
    extract ( $padTree [$tree]  );
    extract ( $occurs  [$occur] );

    if ( count ($occurs) < 2)
      return;

    $parms = [
      'size' => $size,
      'id'   => $id,
      'type' => $type
    ];

    if ( $id == 1 and $action == 'start' )
      padTreeOpen ( 'occurs' );

    padTreeGo ( $childs, $action, 'occur', $parms );

    if ( $id == count ($occurs) and $action == 'end' )
      padTreeClose ( 'occurs' );

  }


  function padTreeGo ( $childs, $action, $tag, $parms ) {

    if ( ! $childs and $action == 'start')
      padTreeLine ( $tag, $parms );
    elseif ( $action == 'start' )
      padTreeOpen ( $tag, $parms );
    else
      padTreeClose ( $tag );

  }


  function padTreeOpen ( $xml, $parms=[] ) {
  
    $more = padTreeMore ( $parms );

    padTreeWrite ( "<$xml$more>" );
  
  }


  function padTreeLine ( $xml, $parms=[] ) {
  
    $more = padTreeMore ( $parms );
 
    padTreeWrite ( "<$xml$more/>" );
  
  }


  function padTreeClose ( $xml ) {
  
    padTreeWrite ( "</$xml>" );
  
  }


  function padTreeWrite ( $xml ) {
  
    global $padTreeFile;

    padFilePutContents ( $padTreeFile, $xml, true );
  
  }


  function padTreeMore ( $parms ) {
  
    $more = '';

    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . htmlspecialchars($value) . '"';

    return $more;
 
    padTreeWrite ( "<$xml$more/>" );
  
  }


  function padTreeTidy () {

    global $padTreeFile;

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
    $tidy->parseString ( padFileGetContents ( padData . $padTreeFile ), $options, 'utf8' );
    $tidy->cleanRepair();

    if ( $tidy === FALSE )
      return padError ( "TIDY conversion error");

    padFilePutContents ( $padTreeFile , $tidy->value );

  }
 

  function padCounter ( $file ) {

    if ( ! file_exists ( padData . $file) )
      padFilePutContents ( $file , '0' );

    $now = padFileGetContents ( padData . $file );

    $now ++;

    padFilePutContents ( $file , $now );  

  }


?>