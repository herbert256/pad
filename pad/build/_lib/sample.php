<?php

  // The designer preview's work (lib/sample.php): the sample of a page loaded and made its
  // variables, and a capture's variables kept and written - read by inits/sample.php when a
  // request asks for the preview or a capture.
  //
  // padSampleName     the two places a page's sample can stand
  // padSampleLoad     the sample of a page: _samples/<name>.json in the page's directory,
  //                   else the capture under DATA/samples/; NULL when there is none
  // padSampleUse      makes the sample's entries variables - not an engine name
  // padSampleBefore   the variables before the PHP runs, for padSampleAfter to compare
  // padSampleAfter    keeps what the PHP made or changed
  // padSampleWrite    writes the capture at the end of the request

  function padSampleName ( $page ) {

    $dir  = str_contains ( $page, '/' ) ? substr ( $page, 0, strrpos ( $page, '/' ) + 1 ) : '';
    $name = basename ( $page );

    return [ APP . $dir . "_samples/$name.json", DATA . 'samples/' . $GLOBALS ['padApp'] . "/$page.json" ];

  }

  function padSampleLoad ( $page ) {

    foreach ( padSampleName ( $page ) as $file ) {

      if ( ! is_file ( $file ) )
        continue;

      $sample = json_decode ( (string) file_get_contents ( $file ), TRUE );

      if ( ! is_array ( $sample ) ) {
        padError ( "the sample " . str_replace ( APPS, '', $file ) . " is not a JSON object" );
        return NULL;
      }

      return $sample;

    }

    return NULL;

  }

  // An engine name - pad*, pq*, _* - is never set from a sample, as it is never set from a
  // request: a sample file is data, it does not configure the engine.

  function padSampleUse ( $sample ) {

    foreach ( $sample as $name => $value )
      if ( padValidVar ( $name ) and ! preg_match ( '/^(pad|pq|_)/', $name ) )
        $GLOBALS [$name] = $value;

  }

  // What a capture keeps: every variable that is not the engine's, holds data rather than
  // an object, and was made or changed by the PHP - compared by a digest of its value.

  function padSampleBefore () {

    $before = [];

    foreach ( $GLOBALS as $name => $value )
      if ( ! preg_match ( '/^(pad|pq|_|GLOBALS$|argv$|argc$)/', $name ) )
        $before [$name] = md5 ( json_encode ( $value, JSON_PARTIAL_OUTPUT_ON_ERROR ) );

    return $before;

  }

  function padSampleAfter ( $before ) {

    global $padSampleData;

    $padSampleData = [];

    foreach ( $GLOBALS as $name => $value ) {

      if ( preg_match ( '/^(pad|pq|_|GLOBALS$|argv$|argc$)/', $name ) )
        continue;

      if ( is_object ( $value ) or is_resource ( $value ) )
        continue;

      if ( isset ( $before [$name] ) and $before [$name] === md5 ( json_encode ( $value, JSON_PARTIAL_OUTPUT_ON_ERROR ) ) )
        continue;

      $padSampleData [$name] = $value;

    }

  }

  // From the shell (pad sample) the capture goes to the application's own _samples/,
  // where it is committed; from the web to DATA/samples/, which the server can write and
  // padSampleLoad reads as well.

  function padSampleWrite () {

    global $padSampleData, $padPage, $padSampleCli;

    if ( ! is_array ( $padSampleData ) )
      return;

    $json = json_encode ( $padSampleData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) . "\n";

    list ( $inApp, $inData ) = padSampleName ( $padPage );

    if ( ! ( $padSampleCli ?? FALSE ) ) {
      padFilePut ( substr ( $inData, strlen ( DATA ) ), $json );
      return;
    }

    if ( ! is_dir ( dirname ( $inApp ) ) )
      mkdir ( dirname ( $inApp ), 0755, TRUE );

    if ( file_put_contents ( $inApp, $json ) === FALSE )
      padError ( "the sample could not be written to $inApp" );
    else
      fwrite ( STDERR, "pad sample: wrote " . str_replace ( APPS, 'apps/', $inApp ) . "\n" );

  }

?>
