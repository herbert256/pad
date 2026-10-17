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
  // padSampleFound    the sample entry a database tag's name= stands for, if any
  //
  // Loading a sample and keeping a capture is build/_lib/sample.php, read by
  // inits/sample.php when the request is one of the two.

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

  function padSampleFound ( $name, &$value ) {

    global $padSampleMode, $padSampleData;

    if ( $padSampleMode != 'use' or ! is_string ( $name ) or $name === '' )
      return FALSE;

    if ( ! is_array ( $padSampleData ) or ! array_key_exists ( $name, $padSampleData ) )
      return FALSE;

    $value = $padSampleData [$name];

    return TRUE;

  }

?>
