<?php


  /**
   * Tidies an XML file in place using PHP tidy extension.
   *
   * @param string $file Path to the XML file.
   *
   * @return void
   */
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


  /**
   * Processes output string, replacing placeholders.
   *
   * Replaces @pad@ with base URL and @self@ with current page URL.
   *
   * @param string $output The output string to process.
   *
   * @return string Processed output.
   */
  function padOutput ( $output ) {

    global $padGo, $padPage;

    $output = padUnescape ( $output );

    $output = str_replace ( '@pad@',  $padGo,            $output );
    $output = str_replace ( '@self@', $padGo . $padPage, $output );

    return $output;

  }


  /**
   * Tidies HTML content using PHP tidy extension.
   *
   * @param string $data     HTML content.
   * @param bool   $fragment If TRUE, output body only.
   *
   * @return string Tidied HTML.
   */
  function padTidy ( $data, $fragment=FALSE ) {

    $config = $GLOBALS ['padTidyConfig'];

    if ( $fragment
         or isset ( $_REQUEST ['padInclude'] )
         or ( isset ( $GLOBALS  ['padInclude'] ) and $GLOBALS ['padInclude'] ) )
      $config ['show-body-only'] = true;

    if ( ! class_exists('tidy') )
      return $data;

    try {
      $tidy = new tidy;
      $tidy->parseString($data, $config, $GLOBALS ['padTidyCcsid'] );
      $tidy->cleanRepair();
      return $tidy->value ?? $data;
    } catch (Throwable $e) {
      return $data;
    }

  }


  /**
   * Sends HTTP header if not already sent.
   *
   * @param string $header Header string.
   *
   * @return void
   */
  function padHeader ($header) {

    if ( headers_sent () )
      return;

    header ($header);

    $GLOBALS ['padHeaders'] [] = $header;

  }


  /**
   * Clears all output buffers and captures content.
   *
   * @param string &$output Receives concatenated buffer contents.
   *
   * @return void
   */
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


  /**
   * Verifies output buffers are empty, errors if not.
   *
   * @return void
   */
  function padCheckBuffers () {

    padEmptyBuffers ( $output );

    if ( trim ( $output ) )
      return padError ( "Illegal output: '$output'" );

  }


?>
