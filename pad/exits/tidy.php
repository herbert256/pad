<?php

  // Cleans up $padOutput just before it is sent: real HTML Tidy when $padTidy is set or
  // the page carries a @tidy@ marker, otherwise PAD's own lightweight pass in
  // exits/myTidy.php.
  //
  // Skipped for padInclude, padExamples and padReference requests, whose output is a
  // fragment that has to stay exactly as it was built.

  if ( isset ( $_REQUEST ['padInclude']   ) ) return;
  if ( isset ( $_REQUEST ['padExamples']  ) ) return;
  if ( isset ( $_REQUEST ['padReference'] ) ) return;

  include PAD . 'config/tidy.php';

  if ( $padTidy or strpos( $padOutput, '@tidy@' ) !== FALSE )

    $padOutput = padTidy ( $padOutput );

  elseif ( $padMyTidy )

    include PAD . 'exits/myTidy.php';

?>