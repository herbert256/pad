<?php

  // Designer preview with sample data. ?orders&padSample renders orders.pad with the
  // variables of _samples/orders.json instead of running the PHP - the page's own, and the
  // _inits.php / _exits.php around it - so a designer works on the template without a
  // database or a login. ?orders&padSample=capture runs the page for real and writes the
  // variables its PHP made to DATA/samples/<app>/<page>.json; `pad sample <app> <page>`
  // (apps/cli/pad) does the same from the shell, into the application's own _samples/.
  //
  // A database tag in the template still asks the database, unless it has a name the
  // sample holds: {array "* from orders", name='orders'} reads the sample's orders. A
  // capture records such a tag's answer under its name too.
  //
  // Skipping the PHP skips the access checks it makes, so $padSample decides who may ask:
  // 'local' (the default) a request this machine makes to itself, TRUE everyone - a design
  // server without real data behind it - and FALSE no one. A capture is for a local
  // request only, whatever the setting: it writes the real data to disk.
  //
  // padSampleMode     '' (a normal request), 'use' or 'capture'
  // padSampleLoad     the sample of a page: _samples/<name>.json in the page's directory,
  //                   else the capture under DATA/samples/; NULL when there is none
  // padSampleUse      makes the sample's entries variables - not an engine name
  // padSampleFound    the sample entry a database tag's name= stands for, if any
  // padSampleBefore   the variables before the PHP runs, for padSampleAfter to compare
  // padSampleAfter    keeps what the PHP made or changed
  // padSampleWrite    writes the capture at the end of the request

  function padSampleMode () {

    global $padSample;

    if ( ! isset ( $_REQUEST ['padSample'] ) or ! is_string ( $_REQUEST ['padSample'] ) )
      return '';

    if ( $_REQUEST ['padSample'] === 'capture' )
      return padLocal () ? 'capture' : '';

    if ( $padSample === TRUE or ( $padSample === 'local' and padLocal () ) )
      return 'use';

    return '';

  }

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

  function padSampleFound ( $name, &$value ) {

    global $padSampleMode, $padSampleData;

    if ( $padSampleMode != 'use' or ! is_string ( $name ) or $name === '' )
      return FALSE;

    if ( ! is_array ( $padSampleData ) or ! array_key_exists ( $name, $padSampleData ) )
      return FALSE;

    $value = $padSampleData [$name];

    return TRUE;

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
