<?php

  function padFileXmlTidy ( $file ) {

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

    $data = padFileGet ( $file );

    if ( ! class_exists('tidy') )
      return;

    try {
      $tidy = new tidy;
      $tidy->parseString ( $data, $options, 'utf8' );
      $tidy->cleanRepair();
      $value = $tidy->value ?? '';
    } catch (Throwable $e) {
      return;
    }

    if ( $value and strlen($value) > 10 )
      padFilePut ( $file, $value, 0 );

  }

  function padOutput ( $output ) {

    global $padGo, $padPage;

    $output = padUnescape ( $output );

    $output = str_replace ( '@pad@',  $padGo,            $output );
    $output = str_replace ( '@self@', $padGo . $padPage, $output );

    return $output;

  }

  function padTidy ( $data, $fragment=FALSE ) {

    global $padInclude, $padTidyCcsid, $padTidyConfig;

    $config = $padTidyConfig;

    if ( $fragment
         or isset ( $_REQUEST ['padInclude'] )
         or ( isset ( $padInclude ) and $padInclude ) )
      $config ['show-body-only'] = true;

    if ( ! class_exists('tidy') )
      return $data;

    try {
      $tidy = new tidy;
      $tidy->parseString($data, $config, $padTidyCcsid );
      $tidy->cleanRepair();
      return $tidy->value ?? $data;
    } catch (Throwable $e) {
      return $data;
    }

  }

  function padHeader ($header) {

    global $padHeaders;

    if ( headers_sent () )
      return;

    header ($header);

    $padHeaders [] = $header;

  }

  function padEmptyBuffers ( &$output ) {

    $output = '';

    set_error_handler ( 'padErrorThrow' );

    try {

      $j = ob_get_level ();

      for ( $i = 1; $i <= $j; $i++ )
        $output .= ob_get_clean ();

    } catch (Throwable $ignored) {

    }

    restore_error_handler ();

  }

  function padCheckBuffers () {

    padEmptyBuffers ( $output );

    if ( trim ( $output ) )
      return padError ( "Illegal output: '$output'" );

  }

?>