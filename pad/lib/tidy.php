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

    if ( ! class_exists ( 'tidy' ) )
      return $data;

    $config = $padTidyConfig;

    if ( $fragment or $padInclude ) {
      $data = trim ( $data );
      $config ['show-body-only'] = true;
    }

    try {
      $tidy = new tidy;
      $tidy->parseString($data, $config, $padTidyCcsid );
      $tidy->cleanRepair();
      return $tidy->value ?? $data;
    } catch (Throwable $e) {
      return $data;
    }

  }

  function padTidySmall ( $data ) {

    global $padTidyCcsid;

    $config = [
      'indent'          => false,     // Disable indentation
      'wrap'            => 0,         // Prevent wrapping lines at a certain length
      'vertical-space'  => false,     // Remove extra empty lines
      'hide-comments'   => true,      // Strip HTML comments
      'tidy-mark'       => false,     // Remove the Tidy meta tag
      'drop-empty-paras'=> true,      // Remove empty <p> tags
      'join-classes'    => true,      // Merge consecutive classes
      'join-styles'     => true,      // Merge consecutive styles
      'show-body-only'  => true,
      'merge-spans'     => 'yes',
      'force-output'    => true,
      'show-warnings'   => FALSE,
      'omit-optional-tags'  => 'yes',
      'merge-divs'      => 'yes',
      'indent-spaces' => 0,
      'drop-empty-elements' => true,
      'drop-proprietary-attributes' => true,
      'new-blocklevel-tags' => '',
      'new-empty-tags' => '',
      'new-inline-tags' => ''
    ];

#    try {

      $tidy = new tidy;
      $tidy->parseString($data, $config, $padTidyCcsid );
      $tidy->cleanRepair();

      $result = $tidy->value ?? $data;

      $result = str_replace  ( ["\r", "\n", "\t"], '', $result );
      $result = preg_replace ( '/ {2,}/', ' ',         $result );
      $result = preg_replace ( '/>\s+</', '><', $result );

      return $result;

 #   } catch (Throwable $e) {

  #    return $data;

   # }

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